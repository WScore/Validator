<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * フィルターごとのエラーメッセージ設定
 */
return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "入力内容を確認してください。",

    // fail for invalid charset string.
    \WScore\Validation\Filters\ValidateUtf8String::INVALID_CHAR => '不正な文字列です。',
    \WScore\Validation\Filters\ValidateUtf8String::ARRAY_INPUT => '入力が配列です。',
    \WScore\Validation\Filters\ValidateInteger::class => '整数を入力してください。',
    \WScore\Validation\Filters\ValidateFloat::class => '数値を入力してください。',
    \WScore\Validation\Filters\ValidateDateTime::class => '日付と認識できません。',

    // error messages for StringLength.
    \WScore\Validation\Filters\StringLength::LENGTH => "文字数は {length} 文字としてください。",
    \WScore\Validation\Filters\StringLength::MAX => "文字数は {max} 文字までで入力してください。",
    \WScore\Validation\Filters\StringLength::MIN => "文字数は {min} 文字以上で入力してください。",

    // error messages for Match
    \WScore\Validation\Filters\Match::IP => '正しいIPアドレスを入力してください。',
    \WScore\Validation\Filters\Match::EMAIL => '正しいメールアドレスを入力してください。',
    \WScore\Validation\Filters\Match::URL => '正しいURLを入力してください。',
    \WScore\Validation\Filters\Match::MAC => '正しいMACアドレスを入力してください。',

    // required value.
    \WScore\Validation\Filters\Required::class => "必須項目です。",

    // ConfirmWith
    \WScore\Validation\Filters\ConfirmWith::MISSING => '確認用の項目に入力してください。',
    \WScore\Validation\Filters\ConfirmWith::DIFFER => '確認用の項目と異なります。',
];