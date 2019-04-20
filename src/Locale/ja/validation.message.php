<?php

/**
 * フィルターごとのエラーメッセージ設定
 */

use WScore\Validation\Filters\ConfirmWith;
use WScore\Validation\Filters\Match;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\StringLength;
use WScore\Validation\Filters\ValidateDateTime;
use WScore\Validation\Filters\ValidateFloat;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Filters\ValidateUtf8String;
use WScore\Validation\Locale\Messages;

return [
    // a fall back error message.
    Messages::class => "入力内容を確認してください。",

    // fail for invalid charset string.
    ValidateUtf8String::INVALID_CHAR => '不正な文字列です。',
    ValidateUtf8String::ARRAY_INPUT => '入力が配列です。',
    ValidateInteger::class => '整数を入力してください。',
    ValidateFloat::class => '数値を入力してください。',
    ValidateDateTime::class => '日付と認識できません。',

    // error messages for StringLength.
    StringLength::LENGTH => "文字数は {length} 文字としてください。",
    StringLength::MAX => "文字数は {max} 文字までで入力してください。",
    StringLength::MIN => "文字数は {min} 文字以上で入力してください。",

    // error messages for Match
    Match::IP => '正しいIPアドレスを入力してください。',
    Match::EMAIL => '正しいメールアドレスを入力してください。',
    Match::URL => '正しいURLを入力してください。',
    Match::MAC => '正しいMACアドレスを入力してください。',

    // required value.
    Required::class => "必須項目です。",

    // ConfirmWith
    ConfirmWith::MISSING => '確認用の項目に入力してください。',
    ConfirmWith::DIFFER => '確認用の項目と異なります。',
];