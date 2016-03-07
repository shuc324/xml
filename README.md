
### XML 读写

```
$xmlIo = new XmlIO();
```

#### 读:

```
$xmlIo->read('./recipe.xml');
```

如果你有如下一个xml文件 './recipe.xml':

```xml
<?xml version="1.0" encoding="UTF-8"?>
<recipe keywords="attribute">
    <preptime>5 minutes</preptime>
    <recipename>Ice Cream Sundae</recipename>
    <ingredlist>
        <listitem>
            <quantity>1</quantity>
            <itemdescription>nuts</itemdescription>
        </listitem>
        <listitem>
            <quantity>1</quantity>
            <itemdescription>cherry</itemdescription>
        </listitem>
    </ingredlist>
</recipe>
```

你将得到这样一个数组:

```array
array (
  'recipe' => 
  array (
    0 => 
    array (
      '@attributes' => 
      array (
        'keywords' => 'attribute',
      ),
      'preptime' => '5 minutes',
      'recipename' => 'Ice Cream Sundae',
      'ingredlist' => 
      array (
        'listitem' => 
        array (
          0 => 
          array (
            'quantity' => '1',
            'itemdescription' => 'nuts',
          ),
          1 => 
          array (
            'quantity' => '1',
            'itemdescription' => 'cherry',
          ),
        ),
      ),
    ),
  ),
)
```

#### 写:

```
$xmlIo->write('./recipe.xml', $xmldata);
```

注: xmldata数据格式参数读时的输出数组格式
