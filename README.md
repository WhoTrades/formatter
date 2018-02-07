# formatter
Universal formatter

Example:

------------------------------------------------------------
Get particular format class instance

```
$format = \whotrades\formatter\Formatter::factory('{
	"root": {
		"node1": ["text1", "text2"],
		"node2": "text3",
		"node3": ["text4", "text5"]
	}
}');
```

------------------------------------------------------------
Get format name

```
var_dump($format->getFormatName());
```

```
string 'json' (length=4)
```

------------------------------------------------------------
Fine nodes using xpath requests
* return value of node if node is scalar, otherwise return node with content

```
var_dump($format->getValueListByXPathList(["//node2", "//node3"]));
```
```
array (size=2)
  0 => string 'text3' (length=5)
  1 => string '{"node3":["text4","text5"]}' (length=27)
```

------------------------------------------------------------
Get as array

```
var_dump($format->getAsArray());
```

```
array (size=1)
  'root' => 
    array (size=3)
      'node1' => 
        array (size=2)
          0 => string 'text1' (length=5)
          1 => string 'text2' (length=5)
      'node2' => string 'text3' (length=5)
      'node3' => 
        array (size=2)
          0 => string 'text4' (length=5)
          1 => string 'text5' (length=5)
```

------------------------------------------------------------
For user friendly rendering

```
var_dump($format->getFormatted());
```

```
string 'Array
(
    [root] => Array
        (
            [node1] => Array
                (
                    [0] => text1
                    [1] => text2
                )

            [node2] => text3
            [node3] => Array
                (
                    [0] => text4
                    [1] => text5
                )

        )

)
' (length=344)
```
