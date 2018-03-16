# 環境
- PHP7.1以上
# 準備
- IP:127.0.0.1 にユーザ名root、パスワード無しのMySQLのDBを用意する
```bash
$ composer install
$ ./app setup
```
# マイグレーション生成
- /database 以下のSQLファイルを編集
- 自動生成を実行
```bash
$ ./app generate
```
- /migrations 以下に変更を行うSQLが生成されているのを確認する
- マイグレーションを実行
```bash
$ ./vendor/bin/phpmig migrate
```