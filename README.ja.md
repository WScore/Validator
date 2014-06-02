WScore.Validation
=================

簡単で多彩なマルチバイトサポートがあるバリデーション・コンポーネント。

簡単に使える、コードを書くのが楽しい、
沢山のエラーメッセージがデフォルトで設定されている、
沢山のバリデーションタイプがデフォルトで存在する、
そしてマルチバイトキャラクター（日本語のこと）を扱いやすい。

その他の特徴は：

*   適用するルールの順番が設定されている。日本語処理に大事な要素。
*   複数の値を一つにまとめる(例： bd_y, bd_m, bd_d to bd)。
*   ロジックを書きやすい。


### ライセンス

MIT License

### インストール

コンポーザーを使ってください。
まだ「dev-master」だけしかありませんが…

```json
"require": {
    "wscore/validation": "dev-master"
}
```


簡単な使い方
----------

コンポーネントの使い方は、**大体**、こんな感じです。

### Factoryクラス（Validationオブジェクトの生成）

コンポーネント内の ```Factory``` クラスを使ってバリデーション用オブジェクトを
生成してください。
バリデーションする入力（配列）は ```source``` メソッドで設定します。フォーム
からの入力を確認するなら、例にあるように ```$_POST``` を使います。

```php
use \WScore\Validation\Factory;
use \WScore\Validation\Rules;

Factory::setLocale('ja');       // use Japanese rules and messages.
$input = Factory::input();      // get validator.
// $input->source( $_POST );    // default is to validate post input.
```


### 入力データのバリデーション

バリデーションの ```is``` メソッドは、バリデーションを行った結果の値を返します。
内容チェックでエラーが有った場合は ```false``` を返します。
これで返り値を使って簡単にロジックを組むことが出来ます。

```php
// check if name or nickname is set
if( !$input->is( 'name', Rules::text() ) ) {
    if( !$input->is( 'nickname', Rules::text() ) ) {
        $input->isError( 'name', 'requires name or nickname' );
    }
}

// check mail with confirmation
$input->is( 'mail', Rules::mail()->sameWith( 'mail2' )->required() );

// check value of input, and do more stuff.
$status = $input->is( 'status', Rules::int()->in( '1', '2', '3' )->required()->message('must be 1-3.') );
if( $status == '1' ) { // add some message?!
    $input->setValue( 'notice', 'how do you like it?' );
} elseif( false === $status ) {
    echo $input->message('status'); // echo 'must be 1-3'
}

if( $input->fails() ) {
    $onlyGoodData    = $input->getSafe();
    $containsBadData = $input->get();
    $message         = $input->message();
} else {
    $goodData = $input->get();
}
```

全ての処理が終了したら、バリデーションされた値は ```get``` メソッドで取得します。
つまり、バリデートされていない値は帰ってこないことになります。


### 値のバリデーション

ひとつの値をバリデーションするには、```verify``` メソッドを使います。

```php
$name  = $input->verify( 'WScore', Rules::text()->string('lower') ); // returns 'wscore'
if( false === $input->verify( 'Bad', Rules::int() ) { // returns false
    echo $input->result()->message(); // echo 'not an integer';
}
```


その他の高度な機能
--------------

### 配列のバリデーション

入力が配列の場合でも対応できます。エラーメッセージも配列になります。

```php
$input->source( array( 'list' => [ '1', '2', 'bad', '4' ] ) );

if( !$input->is( 'list', Rules::int() ) ) {
    $values = $validation->get('list');
    $goods  = $validation->getSafe();
    $errors = $validation->message();
}
/*
 * $values = [ '1', '2', 'bad', '4' ];
 * $goods  = array( 'list' => [ '1', '2', '4' ] );
 * $errors = array( 'list' => [ 2 => 'not an integer' ] );
 */
```


### 複数フィールドの入力

例えば日付のように、複数に分割された入力を一つのように扱えます。

```php
$input->source( [ 'bd_y' => '2001', 'bd_m' => '09', 'bd_d' => '25' ] );
echo $validation->is( 'bd', Rules::date() ); // 2001-09-25
```

### 入力を比較する（SameWith）

パスワードやメールアドレスを入力する際に、
別項目として入力された値と比較することがあります。

```php
$input->source([ 'text1' => '123ABC', 'text2' => '123abc' ] );
echo $validation->is( 'text1', Rules::text()->string('lower')->sameWith('text2') ); // 123abc
```


### フィルターの順番

チェックを行う前に、フィルターする必要がありますよね…

```php
echo $validate->verify( 'ABC', Rules::text()->pattern('[a-c]*')->string('lower'); // 'abc'
## should lower the string first, then check for pattern...
```

### デフォルトのエラーメッセージ

次の手順で、エラーメッセージが決定されます。
1.   messageルールで指定されたメッセージ。あれば必ず使います。
2.   フィルター名とパラメターで指定されたメッセージ。
3.   フィルター名で指定されたメッセージ。
4.   タイプで指定されたメッセージ。
5.   一般的なメッセージ。

#### 例１）```message```フィルターでメッセージを指定します。

メッセージを指定します。

```php
$validate->verify( '', Rules::text()->required()->message('Oops!') );
echo $validate->result()->message(); // 'Oops!'
```

#### 例２）フィルター名とパラメターの定義済みメッセージ

例えば、```matches```というフィルターはパラメータと組み合わせたメッセージが
指定されています。

```php
$validate->verify( '', Rules::text()->required()->matches('code') );
echo $validate->result()->message(); // 'only alpha-numeric characters'
```

#### 例３）フィルター名の定義済みメッセージ

例えば ```required``` や ```sameWith``` は
フィルター名でメッセージが指定されています。

```php
$validate->verify( '', Rules::text()->required() );
echo $validate->result()->message(); // 'required input'
```

#### 例４）タイプで指定されたメッセージ

```php
$validate->verify( '', Rules::date()->required() );
echo $validate->result()->message(); // 'invalid date'
```

#### 例５）一般的なメッセージ

上記のどれにも該当しない場合は、一般的なメッセージを使います。

```php
$validate->verify( '123', Rules::text()->pattern('[abc]') );
echo $validate->result()->message(); // 'invalid input'
```



Predefined Types
----------------

todo: to-be-written

*   text
*   mail
*   number
*   integer
*   float
*   date
*   dateYM
*   datetime
*   time
*   timeHi
*   tel

Predefined Filters
------------------

todo: to-be-write

*   message
*   multiple
*   noNull
*   encoding
*   mbConvert (Ja only)
*   trim
*   sanitize
*   string
*   default
*   required
*   loopBreak
*   code
*   maxlength
*   pattern
*   matches
*   kanaType (ja only)
*   etc.

