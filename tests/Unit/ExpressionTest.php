<?php
namespace StringArgs\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use StringArgs\Expression;

class ExpressionTest extends TestCase
{
    public function testParseExpressionNull()
    {
        $parser = new Expression;
        $this->assertNull($parser->getSourceExpression());
    }

    public function testParseExpressionEmpty()
    {
        $parser = new Expression;
        $parser->parse('');
        $this->assertEquals('none', $parser->getSourceExpression());
        $this->assertTrue(is_array($parser->getArguments()));
        $this->assertCount(0, $parser->getArguments());
    }

    // ARRAY EXPRESSION

    public function testParseInvalidArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        $parser = new Expression;
        $this->assertNull($parser->getSourceExpression());
        $parser->parse("[
            \"one\" => true,()
            \"two\" => intval(\$id),
        ]");
    }

    public function testParseArray()
    {
        $params = [
            '[ "one" => true, "two" => intval($id), "three" => 123, "four" => "string", "five" => null ]',
            "[ 'one' => true, 'two' => intval(\$id), 'three' => 123, 'four' => 'string', 'five' => null ]",
            '[ "one" => true, "two" => intval($id), "three" => 123, "four" => \'string\', "five" => null ]',
            "[ 'one' => true, 'two' => intval(\$id), 'three' => 123, 'four' => \"string\", 'five' => null ]",

            '["one"=>true,"two"=>intval($id),"three"=>123,"four"=>"string","five"=>null]',
            '["one"=>true,"two"=>intval($id),"three"=>123,"four"=>"string","five"=>null]',
            '["one"=>true,"two"=>intval($id),"three"=>123,"four"=>\'string\',"five"=>null]',
            "['one'=>true,'two'=>intval(\$id),'three'=>123,'four'=>\"string\",'five'=>null]",

            '[
                "one" => true,
                "two" => intval($id),
                "three" => 123,
                "four" => "string",
                "five" => null
            ]',
            "[
                'one' => true,
                'two' => intval(\$id),
                'three' => 123,
                'four' => 'string',
                'five' => null
            ]",
            '[
                "one" => true,
                "two" => intval($id),
                "three" => 123,
                "four" => \'string\',
                "five" => null
            ]',
            "[
                'one' => true,
                'two' => intval(\$id),
                'three' => 123,
                'four' => \"string\",
                'five' => null
            ]",

            '["one"         => true,                "two" => intval($id),
                "three"         =>  123,
                "four"      =>      "string",
                "five"      =>      null
            ]',
            "['one'         => true,               'two' => intval(\$id),
                'three'         => 123,
                'four'      => 'string',
                'five'      => null
            ]",
            '[
                "one"   =>  true,
                "two"       =>  intval($id),"three"    => 123,
                "four" => \'string\',                "five" => null]',
            "[  'one' => true,   'two'    =>  intval(\$id),
                'three' =>     123,'four'   => \"string\",
                'five'    => null]",
        ];

        foreach ($params as $expression) {

            // Array inline
            $parser = new Expression;
            $this->assertNull($parser->getSourceExpression());

            $parser->parse($expression);

            $this->assertEquals('array', $parser->getSourceExpression());
            $this->assertTrue(is_array($parser->getArguments()));
            $this->assertCount(5, $parser->getArguments());

            $this->assertEquals([
                "one"   => "true",
                "two"   => "intval(\$id)",
                "three" => "123",
                "four"  => "string",
                "five"  => "null",
            ], $parser->getArguments());
        }
    }

    // JSON EXPRESSION

    public function testParseInvalidJson()
    {
        $this->expectException(\InvalidArgumentException::class);

        $parser = new Expression;
        $this->assertNull($parser->getSourceExpression());
        $parser->parse("{
            \"one\" : true,()
            \"two\" : intval(\$id),
        }");
    }

    public function testParseJson()
    {
        $params = [
            '{ "one" : true, "two" : intval($id), "three" : 123, "four" : "string", "five" : null }',
            "{ 'one' : true, 'two' : intval(\$id), 'three' : 123, 'four' : 'string', 'five' : null }",
            '{ "one" : true, "two" : intval($id), "three" : 123, "four" : \'string\', "five" : null }',
            "{ 'one' : true, 'two' : intval(\$id), 'three' : 123, 'four' : \"string\", 'five' : null }",

            '{"one":true,"two":intval($id),"three":123,"four":"string","five":null}',
            "{'one':true,'two':intval(\$id),'three':123,'four':'string','five':null}",
            '{"one":true,"two":intval($id),"three":123,"four":\'string\',"five":null}',
            "{'one':true,'two':intval(\$id),'three':123,'four':\"string\",'five':null}",

            '{
                "one" : true,
                "two" : intval($id),
                "three" : 123,
                "four" : "string",
                "five" : null
            }',
            "{
                'one' : true,
                'two' : intval(\$id),
                'three' : 123,
                'four' : 'string',
                'five' : null
            }",
            '{
                "one" : true,
                "two" : intval($id),
                "three" : 123,
                "four" : \'string\',
                "five" : null
            }',
            "{
                'one' : true,
                'two' : intval(\$id),
                'three' : 123,
                'four' : \"string\",
                'five' : null
            }",

            "{
                    'one'   :   true,
                'two' :         intval(\$id),          'three' : 123,
                'four'  :    \"string\",
                'five'          :   null }",
        ];

        foreach ($params as $expression) {

            $parser = new Expression;
            $this->assertNull($parser->getSourceExpression());

            $parser->parse($expression);

            $this->assertEquals('json', $parser->getSourceExpression());
            $this->assertTrue(is_array($parser->getArguments()));
            $this->assertCount(5, $parser->getArguments());

            $this->assertEquals([
                "one"   => "true",
                "two"   => "intval(\$id)",
                "three" => "123",
                "four"  => "string",
                "five"  => "null",
            ], $parser->getArguments());
        }
    }

    // INLINE EXPRESSION

    public function testParseInline()
    {
        $params = [
            'true, intval($id), 123, "string", null',
            "true, intval(\$id), 123, 'string', null",
            "
                true,
                intval(\$id),
                123,
                \"string\",
                null
            "
        ];

        foreach ($params as $expression) {

            $parser = new Expression;
            $this->assertNull($parser->getSourceExpression());

            $parser->parse($expression);

            $this->assertEquals('inline', $parser->getSourceExpression());
            $this->assertTrue(is_array($parser->getArguments()));

            $this->assertCount(5, $parser->getArguments());

            $this->assertEquals([
                0 => "true",
                1 => "intval(\$id)",
                2 => "123",
                3 => "string",
                4 => "null",
            ], $parser->getArguments());

            $this->assertEquals('true', $parser->getArgument(0));
            $this->assertEquals("intval(\$id)", $parser->getArgument(1));
            $this->assertEquals("123", $parser->getArgument(2));
            $this->assertEquals("string", $parser->getArgument(3));
            $this->assertEquals("null", $parser->getArgument(4));
        }

    }

    public function testParseInlineQuoteMargins()
    {
        $params = [
            '"xxx", intval($id), 123, "string", "zzz"',
            "'xxx', intval(\$id), 123, 'string', 'zzz'",
            "
                \"xxx\",
                intval(\$id),
                123,
                \"string\",
                \"zzz\"
            ",
            "
                'xxx',
                intval(\$id),
                123,
                'string',
                'zzz'
            "
        ];

        foreach ($params as $expression) {

            $parser = new Expression;
            $this->assertNull($parser->getSourceExpression());

            $parser->parse($expression);

            $this->assertEquals('inline', $parser->getSourceExpression());
            $this->assertTrue(is_array($parser->getArguments()));

            $this->assertCount(5, $parser->getArguments());

            $this->assertEquals([
                0 => "xxx",
                1 => "intval(\$id)",
                2 => "123",
                3 => "string",
                4 => "zzz",
            ], $parser->getArguments());

            $this->assertEquals('xxx', $parser->getArgument(0));
            $this->assertEquals("intval(\$id)", $parser->getArgument(1));
            $this->assertEquals("123", $parser->getArgument(2));
            $this->assertEquals("string", $parser->getArgument(3));
            $this->assertEquals("zzz", $parser->getArgument(4));
        }
    }

    public function testParseInlineNamed()
    {
        $parser = new Expression;
        $this->assertNull($parser->getSourceExpression());

        $parser->setDefaultArgs([
            "one",
            "two",
            "three",
            "four",
            "five"
        ]);
        $parser->parse('true, intval($id), 123, "string", null');

        $this->assertEquals('inline', $parser->getSourceExpression());
        $this->assertTrue(is_array($parser->getArguments()));

        $this->assertCount(5, $parser->getArguments());

        $this->assertEquals([
            "one" => "true",
            "two" => "intval(\$id)",
            "three" => "123",
            "four" => "string",
            "five" => "null",
        ], $parser->getArguments());

        $this->assertEquals("true", $parser->getArgument('one'));
        $this->assertEquals("intval(\$id)", $parser->getArgument('two'));
        $this->assertEquals("123", $parser->getArgument('three'));
        $this->assertEquals("string", $parser->getArgument('four'));
        $this->assertEquals("null", $parser->getArgument('five'));
    }

    public function testAppendArguments()
    {
        $parser = new Expression;
        $this->assertNull($parser->getSourceExpression());

        $parser->setDefaultArgs(['class', 'style', 'id']);
        $parser->addArgument('class', 'btn');
        $parser->setAppendArgs(['class', 'style']);
        $parser->parse('"btn-success", "color", "meu-id"');
        $parser->addArgument('class', 'disabled');
        $parser->addArgument('id', 'meu-id-sobrescrito');

        $this->assertEquals('inline', $parser->getSourceExpression());
        $this->assertTrue(is_array($parser->getArguments()));
        $this->assertEquals([
            "class" => 'btn btn-success disabled',
            "style" => 'color',
            "id"    => 'meu-id-sobrescrito',
        ], $parser->getArguments());
    }
}
