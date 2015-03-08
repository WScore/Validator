Validation
==========

マルチバイトサポート（というより日本語）が豊富なValidationコンポーネント。

*   できるだけコード補完、
*   沢山のバリデーションタイプがデフォルトで存在する、
*   そしてマルチバイトキャラクター（日本語のこと）を扱いやすい。

その他の特徴は：

*   適用するルールの順番が設定されている。日本語処理に大事な要素。
*   配列入力もバリデーション可能。
*   複数の値を一つにまとめる(例： bd_y, bd_m, bd_d → bd)。
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

コンポーネント内の ```ValidationFactory``` クラスを使ってバリデーション用オブジェクトを生成してください。

```php
use \WScore\Validation\ValidationFactory;

$factory = ValidationFactory();     // use English rules and messages.
$factory = ValidationFactory('ja'); // use Japanese rules and messages.
```

データ配列のバリデーションは```on```メソッドから。

```php
$input = $factory->on($_POST);
$input->asText('name');
$input->asInteger('age');
$input->asDate('bate');
if($v->fails()) {
   $messages = $input->messages();
}
$values = $input->get();
```


### 入力データのバリデーション

バリデーションの ```as{Type}($name)``` メソッドは、データの中の```$name```に対してバリデーションルールを設定します。

```php
$input = $factory->on( $_POST );   // get validator.
$input->asText('name')->required() );
$input->asMail('mail')->required()->sameWith('mail2'));
$found = $input->get(); // [ 'name' => some name... ]
if( $input->fails() ) {
    $onlyGoodData    = $input->getSafe();
    $containsBadData = $input->get();
} else {
    $onlyGoodData    = $input->get();
}
```

バリデーションが成功したかどうか ```fails()``` を
使って確認できます（```passes()```メソッドもあります）。

処理完了後、```get``` メソッドで値を取得します。ただし不正な
値も全て含まれるので、正しい値だけを取得するには ```getSafe()```
を使って下さい。



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
$input->asInteger('list');
if( !$input->passes() ) {
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

自作の複数フィールドの入力フィルターを作る場合は、```multiple``` を使います。

```php
$input->asText('ranges')->multiple( [
    'suffix' => 'y1,m1,y2,m2',
    'format' => '%04d/%02d - %04d/%02d'
] );
```

ここで、```suffix``` は入力の最後のサフィックス部分、
そして ```format``` が配列を文字列に変換するフォーマット
（sprintfを利用）になります。.


### 入力を比較する（SameWith）

パスワードやメールアドレスを入力する際に、
別項目として入力された値と比較することがあります。

```php
$input->source([ 'text1' => '123ABC', 'text2' => '123abc' ] );
echo $validation->asText('text1')
	->string('lower')
	->sameWith('text2') ); // 123abc
```


### フィルターの順番

チェックを行う前に、フィルターする必要がありますよね…

```php
echo $validate->verify( 'ABC', Rules::text()->pattern('[a-c]*')->string('lower'); // 'abc'
## should lower the string first, then check for pattern...
```



### 自作バリデーションフィルター

自作のフィルターを利用するには ```closure``` 関数を利用します。

```php
/**
 * @param ValueTO $v
 */
$filter = function( $v ) {
    $val = $v->getValue();
    $val .= ':customized!';
    $v->setValue( $val );
    $v->setError(__METHOD__);
    $v->setMessage('Closure with Error');
};
Rules::text()->custom( $filter );
```

自作フィルターにはクロージャー以外のパラメターは渡せません
（クロージャー自体がパラメタです）。引数はひとつで、ValueTO
オブジェクトになります。これを利用して、値・エラー・メッセージ
などを操作してください。

エラーを設定したら（値は文字なら何でもいいのですが、
```__METHOD__``` が適当かもしれません）。これでフィルターのループ
が途切れ、これ以降のバリデーションは行いません。


デフォルトのエラーメッセージ
----------------------

次の手順で、エラーメッセージが決定されます。

1.   messageルールで指定されたメッセージ。あれば必ず使います。
2.   フィルター名とパラメターで指定されたメッセージ。
3.   フィルター名で指定されたメッセージ。
4.   タイプで指定されたメッセージ。
5.   一般的なメッセージ。

### 例１）```message```フィルターでメッセージを指定します。

メッセージを指定します。

```php
$validate->verify( '', Rules::text()->required()->message('Oops!') );
echo $validate->result()->message(); // 'Oops!'
```

### 例２）フィルター名とパラメターの定義済みメッセージ

例えば、```matches```というフィルターはパラメータと組み合わせたメッセージが
指定されています。

```php
$validate->verify( '', Rules::text()->required()->matches('code') );
echo $validate->result()->message(); // 'only alpha-numeric characters'
```

### 例３）フィルター名の定義済みメッセージ

例えば ```required``` や ```sameWith``` は
フィルター名でメッセージが指定されています。

```php
$validate->verify( '', Rules::text()->required() );
echo $validate->result()->message(); // 'required input'
```

### 例４）タイプで指定されたメッセージ

```php
$validate->verify( '', Rules::date()->required() );
echo $validate->result()->message(); // 'invalid date'
```

### 例５）一般的なメッセージ

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

