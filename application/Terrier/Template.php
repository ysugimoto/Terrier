<?php

namespace Terrier;

class Template
{
    public static function make($templateName)
    {
        if ( ! file_exists(TEMPLATE_PATH . $templateName . '.html') )
        {
            Log::write('Template: ' . $templateName . ' is not found.', Log::LEVEL_WARN);
            throw new Exception('Template: ' . $templateName . ' is not found.');
        }

        $buffer   = file_get_contents(TEMPLATE_PATH . $templateName . '.html');
        $template = new Template($buffer, dirname(TEMPLATE_PATH . $templateName . '.html'));

        $template->compile();

        return $template;
    }

    protected $template;
    protected $baseDir;
    protected $syntax   = array('obj');
    protected $counter  = 0;
    protected $division = true;
    protected $templateFunction;

    public function __construct($template, $baseDir = '.')
    {
        $this->template = $template;
        $this->baseDir  = $baseDir;
    }

    public function parse()
    {
        if ( ! is_callable($this->templateFunction, TRUE) )
        {
            throw new Exception('Template::parse: template is not compiled yet');
        }

        return call_user_func_array($this->templateFunction, func_get_args());
    }

    public function compile($partial = FALSE)
    {
        $compile = array();
        $index = 0;
        $nest = 0;

        preg_match_all('/\{\{([\/#@%])?(.+?)\}\}/', $this->template, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ( $matches as $match )
        {
            list($all, $signature, $content) = $match;
            $context = substr($this->template, $index, $all[1] - $index);
            if ( $context && ! preg_match('/\A[\r\n\s]+\z/', $context) )
            {
                if ( $nest > 0 )
                {
                    $context = preg_replace('/^[\n\r]+|[\n\r]+$/', '', $context);
                }
                $compile[] = $this->getPrefix() . $this->quote($context);
            }
            $index = $all[1] + strlen($all[0]);

            switch ( $signature[0] )
            {
                case '#':
                    $compile[] = $this->getPrefix() . $this->_compileHelper($content[0]);
                    break;

                case '@':
                    $v = $this->_compileReservedVars($content[0]);
                    if ( $v )
                    {
                        $compile[] = $this->getPrefix() . $v;
                    }
                    break;

                case '%':
                    $compile[] = $this->getPrefix() . $this->geteSyntax($content[0]);
                    break;

                case '/':
                    if ( $content[0] === 'for' )
                    {
                        array_pop($this->syntax);
                        $this->counter++;
                    }
                    $compile[] = ';}';
                    $this->division = true;
                    $nest--;
                    break;

                default:
                    $v = $this->_compileBuiltInControl($content[0]);
                    if ( $v )
                    {
                        $compile[] = $v;
                    }
                    if ( preg_match('/^(for|if)/', $content[0]) )
                    {
                        $nest++;
                    }
                    break;
            } // swtich
        } // foreach

        if ( $index < strlen($this->template) )
        {
            $compile[] = $this->getPrefix() . $this->quote(substr($this->template, $index));
        }

        if ( $partial === TRUE )
        {
            return implode('', $compile);
        }

        $compile[] = ';return $b;';
        //echo '<pre>';
        //var_dump(implode('', $compile));
        //echo '</pre>';

        $this->templateFunction = create_function('$obj,$b=""', implode('', $compile));
    }

    protected function _compileReservedVars($sentence)
    {
        $isEscape = true;

        if ( $sentence{0} === '%' )
        {
            $sentence = substr($sentence, 1);
            $isEscape = false;
        }

        if ( ! preg_match('/\A(data|value|index|parent)(.+)?/', $sentence, $match) )
        {
            return;
        }

        switch ( $match[1] )
        {
            case 'data':
            case 'value':
                //$value = $this->getSyntax() . ((isset($match[2])) ? $match[2] : '');
                $value    = '$v' . ($this->counter - 1);
                break;

            case 'parent':
                $p     = array_slice($this->syntax, 0, -1);
                $value = '$' . implode('->', $p) . ((isset($match[2])) ? $match[2] : '');
                break;

            case 'index':
                $value    = '$i' . ($this->counter - 1);
                break;

            default:
                return;
        }

        return ( $isEscape ) ? "\\Terrier\\Helper::escape(" . $value . ")" : $value;
    }

    protected function _compileBuiltInControl($sentence)
    {
        if ( ! preg_match('/^(if|else\sif|else|for|include)(?:\s(.+))?/', $sentence, $match) )
        {
            return $this->getPrefix() . "\\Terrier\\Helper::escape(" . $this->getSyntax($sentence) . ")";
        }

        $this->division = true;
        switch ( $match[1] )
        {
            case 'if':
                return ';if(' . $this->_parseCondition($match[2]) . '){';

            case 'else if':
                return '}else if(' . $this->_parseCondition($match[2]) . '){';

            case 'else':
                return '}else{';

            case 'for':
                $c   = $this->counter++;
                $ic  = '$i' . $c;
                $vc  = '$v' . $c;
                $tmp = ';foreach(' . $this->getSyntax($match[2]) . '->get() as ' . $ic . '=>' . $vc .'){' . $vc . '=new \\Terrier\\Variable(' . $vc .');';
                $this->syntax[] = $match[2] . '[$i' . $this->counter . ']';
                return $tmp;

            case 'include':
                $path = $this->baseDir . '/' . ltrim($matches[2], '/');
                if ( file_exists($path) )
                {
                    $buffer = file_get_contents($path);
                    $tmpl   = new Template($buffer, dirname($path));
                    return $tmpl->compile(TRUE);
                }
                return '';
        }
    }

    protected function quote($str)
    {
        $grep = array("\n",  '"',   "\r");
        $sed  = array("\\n", '\\"', "\\r");

        return '"' . str_replace($grep, $sed, $str) . '"';
    }

    protected function getPrefix()
    {
        $prefix = '.';

        if ( $this->division )
        {
            $prefix = '$b.=';
            $this->division = false;
        }

        return $prefix;
    }

    protected function _parseCondition($condition)
    {
        $token  = preg_replace('/(!|>=?|<=?|={2,3}|[^\+]\+|[^\-]\-|\*|&{2}|\|{2})/', ' $1 ', $condition);
        $tokens = preg_split('/\s+/', $token);
        $cond   = array();


        foreach ( $tokens as $t )
        {
            if ( $t === '' )
            {
                continue;
            }
            else if ( preg_match('/\A(!|>=?|<=?|={1,3}|\+|\-|\*|&{2}|\|{2})\z/', $t) )
            {
                $cond[] = $t;
            }
            else
            {
                $p = $this->getPrimitiveType($t);
                if ( is_null($p) )
                {
                    $cond[] = $this->getSyntax($t);
                }
                else if ( is_int($p) || is_float($p) )
                {
                    $cond[] = $p;
                }
                else if ( is_string($p) )
                {
                    $cond[] = $this->quote($p);
                }
            }
        }

        return implode(' ', $cond);
    }

    protected function _compileHelper($sentence)
    {
        $args = preg_split('/\s+/', $sentence);
        for ( $i = 0; $i < count($args); ++$i ) {
            if ( preg_match('/\\\\$/', $args[$i]) ) {
                $args[$i] = rtrim($args[$i], '\\');
                if ( isset($args[$i + 1]) )
                {
                    $args[$i] .= ' ' . $args[$i + 1];
                    array_splice($args, $i + 1);
                }
            }
        }
        $function = array_shift($args);

        if ( ! function_exists($function) )
        {
            throw new Exception('Parse Error: "' . $function . '" is undefined or not a function.');
        }

        foreach ( $args as $index => $arg )
        {
            $p = $this->getPrimitiveType($arg);
            if ( $p === null )
            {
                $args[$index] = $this->getSyntax($arg);
            }
            else if ( is_string($arg) )
            {
                $args[$index] = $this->quote($p);
            }
            else if ( is_int($p) || is_float($p) )
            {
                $args[$index] = $p;
            }
        }

        return $function . '(' . implode(',', $args) . ')';

    }

    protected function getSyntax($prop = '')
    {
        $syntax = '$' . implode('->', $this->syntax);
        if ( $prop )
        {
            $prop = str_replace('.', '->', $prop);
            $prop = preg_replace('/@([0-9a-zA-Z\-_]+)/', "['$1']", $prop);
            $syntax .= '->' . $prop;
        }

        return $syntax;
    }

    protected function getPrimitiveType($value)
    {
        if ( preg_match('/\A[\'"](.*?)[\'"]\z/', $value, $match) )
        {
            return $match[1];
        }
        else if ( preg_match('/\A([0-9\.]+?)\z/', $value, $match) )
        {
            return ( strpos($match[1], '.') !== FALSE ) ? floatval($match[1]) : intval($match[1]);
        }

        return null;
    }
}

