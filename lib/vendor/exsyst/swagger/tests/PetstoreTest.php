<?php

/*
 * This file is part of the Swagger package.
 *
 * (c) EXSyst
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EXSyst\Component\Swagger\tests;

use EXSyst\Component\Swagger\Schema;
use EXSyst\Component\Swagger\Swagger;
use PHPUnit\Framework\TestCase;

class PetstoreTest extends TestCase
{
    private function fileToArray($filename)
    {
        return json_decode(file_get_contents($filename), true);
    }

    public function testMinimal()
    {
        $filename = __DIR__.'/fixtures/petstore-minimal.json';
        $swagger = Swagger::fromFile($filename);

        $this->assertEquals($this->fileToArray($filename), $swagger->toArray());
    }

    public function testSimple()
    {
        $filename = __DIR__.'/fixtures/petstore-simple.json';
        $swagger = Swagger::fromFile($filename);

        $this->assertEquals($this->fileToArray($filename), $swagger->toArray());
    }

    public function testParameterRefs()
    {
        $filename = __DIR__.'/fixtures/petstore-parameter-refs.json';
        $swagger = Swagger::fromFile($filename);

        $this->assertEquals($this->fileToArray($filename), $swagger->toArray());
    }

    public function testPetstore()
    {
        $filename = __DIR__.'/fixtures/petstore.json';
        $swagger = Swagger::fromFile($filename);

        $this->assertEquals($this->fileToArray($filename), $swagger->toArray());

        $responses = $swagger->getPaths()->get('/pets')->getOperation('get')->getResponses();
        $headers = $responses->get('200')->getHeaders();

        $this->assertEquals(1, count($headers->toArray()));
        $this->assertTrue($headers->has('x-expires'));
        $expires = $headers->get('x-expires');

        $this->assertEquals('string', $expires->getType());

        $headers->remove('x-expires');
        $this->assertNull($headers->toArray());
        $this->assertFalse($headers->has('x-expires'));

        $headers->set('x-expires', $expires);
        $this->assertCount(1, $headers->toArray());
        $this->assertTrue($headers->has('x-expires'));
    }

    public function testExpanded()
    {
        $filename = __DIR__.'/fixtures/petstore-expanded.json';
        $swagger = Swagger::fromFile($filename);

        $this->assertEquals($this->fileToArray($filename), $swagger->toArray());
    }

    public function testExternalDocs()
    {
        $filename = __DIR__.'/fixtures/petstore-with-external-docs.json';
        $swagger = Swagger::fromFile($filename);

        $this->assertEquals($this->fileToArray($filename), $swagger->toArray());

        $external = $swagger->getExternalDocs();
        $this->assertEquals('find more info here', $external->getDescription());
        $this->assertEquals('https://Swagger.io/about', $external->getUrl());

        $info = $swagger->getInfo();
        $this->assertEquals('1.0.0', $info->getVersion());
        $this->assertEquals('Swagger Petstore', $info->getTitle());
        $this->assertEquals('A sample API that uses a petstore as an example to demonstrate features in the Swagger-2.0 specification', $info->getDescription());
        $this->assertEquals('http://Swagger.io/terms/', $info->getTerms());

        $this->assertEquals('1.0.1', $info->setVersion('1.0.1')->getVersion());
        $this->assertEquals('Pets', $info->setTitle('Pets')->getTitle());
        $this->assertEquals('desc', $info->setDescription('desc')->getDescription());
        $this->assertEquals('T-O-S', $info->setTerms('T-O-S')->getTerms());

        $contact = $info->getContact();
        $this->assertEquals('Swagger API Team', $contact->getName());
        $this->assertEquals('apiteam@Swagger.io', $contact->getEmail());
        $this->assertEquals('http://Swagger.io', $contact->getUrl());

        $this->assertEquals('Swaggers', $contact->setName('Swaggers')->getName());
        $this->assertEquals('team@Swagger.io', $contact->setEmail('team@Swagger.io')->getEmail());
        $this->assertEquals('https://Swagger.io', $contact->setUrl('https://Swagger.io')->getUrl());

        $license = $info->getLicense();
        $this->assertEquals('MIT', $license->getName());
        $this->assertEquals('http://github.com/gruntjs/grunt/blob/master/LICENSE-MIT', $license->getUrl());

        $this->assertEquals('APL', $license->setName('APL')->getName());
        $this->assertEquals('https://www.apache.org/licenses/LICENSE-2.0', $license->setUrl('https://www.apache.org/licenses/LICENSE-2.0')->getUrl());
    }

    public function testDictionaries()
    {
        $filename = __DIR__.'/fixtures/petstore-dictionaries.json';
        $swagger = Swagger::fromFile($filename);

        $swaggerAsArray = $swagger->toArray();

        $this->assertEquals($this->fileToArray($filename), $swaggerAsArray);
        $this->assertInstanceOf(Schema::class, $swagger->getDefinitions()->get('Pet')->getProperties()->get('sub-object')->getAdditionalProperties());
        $this->assertSame(true, $swaggerAsArray['definitions']['Pet']['properties']['sub-object']['additionalProperties']);
        $this->assertSame(null, $swagger->getDefinitions()->get('Pet')->getProperties()->get('sub-object')->getAdditionalProperties()->getType());
        $this->assertSame(null, $swagger->getDefinitions()->get('Pet')->getProperties()->get('sub-object')->getAdditionalProperties()->getAdditionalProperties());
        $this->assertSame('string', $swagger->getDefinitions()->get('Pet')->getProperties()->get('another-sub-object')->getAdditionalProperties()->getType());
        $this->assertSame(null, $swagger->getDefinitions()->get('Pet')->getProperties()->get('another-sub-object')->getAdditionalProperties()->getAdditionalProperties());
        $this->assertSame('object', $swagger->getDefinitions()->get('Pet')->getProperties()->get('nested-sub-objects')->getAdditionalProperties()->getType());
        $this->assertInstanceOf(Schema::class, $swagger->getDefinitions()->get('Pet')->getProperties()->get('nested-sub-objects')->getAdditionalProperties()->getAdditionalProperties());
        $this->assertSame('string', $swagger->getDefinitions()->get('Pet')->getProperties()->get('nested-sub-objects')->getAdditionalProperties()->getAdditionalProperties()->getType());
        $this->assertSame('string', $swaggerAsArray['definitions']['Pet']['properties']['nested-sub-objects']['additionalProperties']['additionalProperties']['type']);
        $this->assertSame('array', $swagger->getDefinitions()->get('Pet')->getProperties()->get('children')->getType());
        $this->assertSame('array', $swaggerAsArray['definitions']['Pet']['properties']['children']['type']);
        $this->assertInstanceOf(Schema::class, $swagger->getDefinitions()->get('Pet')->getProperties()->get('children')->getItems());
        $this->assertSame('#/definitions/Pet', $swagger->getDefinitions()->get('Pet')->getProperties()->get('children')->getItems()->getRef());
        $this->assertSame('#/definitions/Pet', $swaggerAsArray['definitions']['Pet']['properties']['children']['items']['$ref']);
        $this->assertSame('object', $swaggerAsArray['definitions']['Pet']['properties']['child-pet']['type']);
        $this->assertInstanceOf(Schema::class, $swagger->getDefinitions()->get('Pet')->getProperties()->get('child-pet')->getProperties()->get('name'));
        $this->assertSame('string', $swagger->getDefinitions()->get('Pet')->getProperties()->get('child-pet')->getAdditionalProperties()->getProperties()->get('name')->getType());
        $this->assertSame('string', $swaggerAsArray['definitions']['Pet']['properties']['child-pet']['additionalProperties']['properties']['name']['type']);
        $this->assertSame('object', $swagger->getDefinitions()->get('Pet')->getProperties()->get('child-pet')->getAdditionalProperties()->getProperties()->get('child-pet-children')->getType());
        $this->assertSame('string', $swagger->getDefinitions()->get('Pet')->getProperties()->get('child-pet')->getAdditionalProperties()->getProperties()->get('child-pet-children')->getAdditionalProperties()->getType());
        $this->assertSame('object', $swagger->getDefinitions()->get('Pet')->getProperties()->get('child-pet')->getAdditionalProperties()->getProperties()->get('sub-object')->getType());
        $this->assertSame(true, $swaggerAsArray['definitions']['Pet']['properties']['child-pet']['additionalProperties']['properties']['sub-object']['additionalProperties']);
    }
}
