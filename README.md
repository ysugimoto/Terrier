Terrier: PHP mailform Application (beta)
=======

TerrierはPHPで書かれたシンプルなメールフォームアプリケーションです。
設定ファイルのみで動作の変更が可能、またテンプレートエンジンを利用して各画面を要件に合わせて構築が可能です。

**現在テスト項目が完全でないためベータリリースです**

## Features

- 他の外部ライブラリに依存していないので、単体で動かすことが可能です。
- PHPとして即時コンパイル、実行するテンプレートエンジンが実装されています。HTMLファイルそのままを各画面で扱うことが可能です。
- 規約より設定を重視しているため、設定項目のみで様々な動作の制御が可能です。PHPを書く必要はありません。
- 関数ベースでテンプレートヘルパを拡張することも可能です。
- PHPのmail()関数、SMTPサーバ経由の送信に両対応しています。
- コア以外のファイル構造は自由に変更できます。
- オートセッションとCSRFトークンチェック機能が行われます。

## Requirement

PHP5.3+

## Installation

### Git

このリポジトリをクローンし、初期化コマンドを実行してください。

```
$ git clone https://github.com/ysugimoto/Terrier.git
$ cd Terrier
$ ./terrier init
```

設定ファイルの生成、一時ディレクトリの書き込み権限を発行します。

### Archive

アーカイブをダウンロードして、任意の場所に展開してください。初期化コマンドを発行するか、以下の処理を行ってください。

- `application/config_sample`を`application/config'にリネーム
- `application/tmp`に書き込み権限を発行

※書き込み権限はファイルアップロード、ロギング処理を行わない場合は不要です。

`http://localhost/Terrier/index.php`など設置した場所にアクセスして、動作を確認してください。

## Configuration

Terrierは`application/config/`以下の設定ファイルを変更することでメールフォームの設定と動作の制御を行うことができます：

- `config/config.php`: アプリメインの設定項目
- `config/setting.php`: 入力項目とバリデーションの設定
- `config/upload.php`: ファイルアップロードの設定
- `config/mail.php`: メール送信に関する設定

詳細は各ファイルのセクションコメントを参照してください。（ドキュメント準備中）

## Templates

Terrierは`application/templates/`以下のファイルをテンプレートとして扱います。`templates`のディレクトリは自由な位置に変更可能、ヘルパ関数群の`functions.php`を除いてHTML/TEXTファイルそのままです。

- `templates/input.html`: 入力画面のテンプレート
- `templates/confirm.html`: 確認画面のテンプレート
- `templates/complete.html`: 完了画面のテンプレート
- `templates/error.html`: システムエラーのテンプレート
- `templates/mailbody.txt`: 管理者へ通知するメール本文
- `templates/reply.txt`: 送信者への自動返信する際のメール本文
- `templates/functions.php`: テンプレート内で利用するヘルパ関数定義ファイル`

### Template Tags

テンプレートはJavaScriptのHandlebarsのように`{{``}}`で括った中身に変数をバインドします。
テンプレート内では入力データやバリデーションエラーの他に環境変数など様々な値が使用できます（ドキュメント整備中）。また、変数は自動でエスケープされます。

## License

MITライセンスに従って配布しています。ライセンスの規約内で自由にご利用ください。

## Attension

- 本アプリケーションの利用は自己責任にてお願いします。
- 本アプリケーションを利用した事による事柄に対し、作者は一切の責任を負いません。

## Issues

- 本アプリケーションの改善に際し、要望頂ければできるだけ反映したいと思っております。
- 要望やバグ報告など、issuesに投稿をお願いします。




