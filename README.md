# What's This?
这是一个为方便初级 phper 使用 php 实现自动依赖注入开发的库。

### 获取实例
```php
namespace test;
require './vendor/autoload.php';


class a {

    public $b;

    public function __construct(\test\b $obj) {
        $this->b = $obj;
    }

    public function name() {
        echo 'a';
    }

}

class b {

    public function name() {
        echo 'b';
    }

}

$app = new \Lisao\Ioc\Ioc::getInstance('\test\a');
$app->b->name();

//输出
b
```

