# Zusuky framework  
速くて軽い、WEBサイト構築用のPHPフレームワークです。

<br>

## 1. Dockerで動かす
Zusuky on docker.zipを解凍の上、docsに記載に従えばDocke上で動かすことが可能です。

<br>

## 2. サンプルアプリの導入方法
以下の機能が実装されているサンプルアプリの導入方法です。
- ユーザ登録
- ログイン

### 2-1. アプリケーションの上書き
sample_app.zipを解凍し、解凍ファイルに内のappフォルダの内容で、appフォルダを上書する。

### 2-2. テーブルの作成
CREATE_TABLE.ddlに記述されているSQLを実行し、テーブルを作成する。

### 2-3. サンプル表示
フロント画面用のユーザ登録&ログイン確認

```
http://localhost
```

管理画面用ユーザの登録&ログイン確認

```
http://localhost/admin/
```
