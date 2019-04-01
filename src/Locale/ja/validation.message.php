<?php

return [
    // a fall back error message.
    \WScore\Validation\Locale\Messages::class => "入力内容を確認してください",

    // error messages for FilterInterfaces.
    \WScore\Validation\Filters\StringLength::class => "最大文字数は{max}字です",
    \WScore\Validation\Filters\Required::class => "必須項目です",
];