<?php
/*
 * Jeans CMS (GPL license)
 * $Id: japanese.php 295 2010-10-18 19:42:49Z kmorimatsu $
 */

define('_INSTALL_LANGUAGE',substr(basename(__FILE__),0,-4));

define('_INSTALL_WELCOME','Jeans CMSの世界へようこそ！');
define('_INSTALL_SELECT_LANGUAGE','インストールに用いる言語を選択してください。');
define('_INSTALL_CONTINUE','続ける');
define('_INSTALL_INSTALL','インストール');
define('_INSTALL_SET_PERMISSION','インストールを開始する前に、sqliteディレクトリのパーミッションが、読み書き可能な値（777など）になっていることを確認してください。');
define('_INSTALL_PERMISSION_OK','sqliteディレクトリのパーミッションは、適正にセットされているようです。');
define('_INSTALL_PERMISSION_NG','sqliteディレクトリのパーミッションが、適正にセットされていないかもしれません。');
define('_INSTALL_INPUT_INFORMATION','Jeans CMSのインストールに必要な情報を入力してください。');
define('_INSTALL_SITE_NAME','サイト名');
define('_INSTALL_TIME_ZONE','タイムゾーン');
define('_INSTALL_YOUR_EMAIL','メールアドレス');
define('_INSTALL_YOUR_LOGINNAME','ログイン名（英数字,非公開）');
define('_INSTALL_YOUR_NAME','名前（全角可。公開されます）');
define('_INSTALL_PASSWORD','パスワード');
define('_INSTALL_PASSWORD_AGAIN','パスワード（確認入力）');

define('_INSTALL_GENERAL','一般');
define('_INSTALL_ITEM_TITLE','Jeans CMS バージョン'._JEANS_VERSION.' へようこそ');
define('_INSTALL_ITEM_BODY','ウェブページの作成を補助する積み木がここにあります。
それは心躍るblogになるかもしれませんし、観るものを和ませる家族のページになるかもしれませんし、実り多き趣味のサイトになるかもしれません。
あるいは現在のあなたには想像がつかないものになることだってあるでしょう。用途が思いつきませんでしたか？
それならここへ来て正解です。なぜならあなた同様私たちにもわからないのですから。');
define('_INSTALL_ITEM_MORE','<br /><br />この記事を削除することもできますが、どちらにせよ記事を追加していくことによってやがてメインページからは見えなくなります。
Jeans CMSを扱ううちに生じたメモをコメントとして追加し、将来アクセスできるようにこのページをブックマークしておくのも手です。');

define('_INSTALL_CONGRATULATIONS','おめでとうございます！');
define('_INSTALL_DONE','Jeans CMSのインストールは完了しました。');
define('_INSTALL_GOTO_SITE','出来上がったサイトを見るには、次のリンクをクリックしてください。');
define('_INSTALL_GOTO_ADMIN','サイトの各種設定を行うには、次のリンクをクリックしてください。');

define('_INSTALL_FAILED','インストールに失敗');
define('_INSTALL_FAILED_UNFORTUNATELY','残念ですが、Jeans CMSのインストールに失敗しました。');
define('_INSTALL_FAILED_DESCRIPTION','インストールをやり直す場合、sqliteディレクトリのパーミッションをチェックし、
必要であれば、sqliteディレクトリに作成された.htdbsqliteと.htdbloginの２つのファイルを削除してから行ってください。');
