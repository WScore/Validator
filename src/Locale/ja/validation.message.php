<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * フィルターごとのエラーメッセージ設定
 */
return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "入力内容を確認してください。",

    // fail for invalid charset string.
    \WScore\Validation\Filters\FilterValidUtf8::INVALID_CHAR => '不正な文字列です。',
    \WScore\Validation\Filters\FilterValidUtf8::ARRAY_INPUT => '入力が配列です。',
    \WScore\Validation\Filters\FilterArrayToValue::MISSING_FIELD => '配列「{field}」が存在しません。',

    // error messages for Convert Filters.
    \WScore\Validation\Filters\ConvertDateTime::class => '日付と認識できません。',

    // error messages for StringLength.
    \WScore\Validation\Filters\StringLength::LENGTH => "文字数は {length} 文字としてください。",
    \WScore\Validation\Filters\StringLength::MAX => "文字数が {max}文字以上です。",
    \WScore\Validation\Filters\StringLength::MIN => "文字数が {min}文字以下です。",

    // required value.
    \WScore\Validation\Filters\Required::class => "必須項目です。",
];