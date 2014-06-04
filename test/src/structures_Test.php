<?php
require_once (DIR_BASE.'/class/structures_do.php');

class structures extends PHPUnit_Framework_TestCase
{
    // ...

    public function testLoadList()
    {
        /* Load data */
        // Arrange
        $a = new structures_do();
        $a->load(DIR_BASE.'/test/data/structures_demo.json');

        // Act
        $b = $a->getAllStructures();

        // Assert
        $this->assertEquals(2, count($b));

        //TODO Load data from invalid source


        /* Add structures */
        // Arrange
        $a = new structures_do();
        //$a->load(DIR_BASE.'/test/data/structures_demo.json');
        $numAdd = 5;
        for ($n = 0; $n < $numAdd; $n++) {
            $new_structure = new structure_do();
            $new_structure->setId("foo $n");
            $new_structure->setName("Name foo $n");
            $new_structure->setStorage('text/plain');
            $a->add($new_structure);
        }

        // Act
        $b = $a->getAllStructures();
        $c = $a->get('foo 3');

        // Assert
        $this->assertEquals(5, count($b));
        $this->assertEquals($c->getId(), 'foo 3');

        // Arrange
        $a = new structures_do();
        $new_structure1 = new structure_do();
        $new_structure1->setId("foo");
        $new_structure1->setName("Name foo");
        $new_structure1->setStorage('text/plain');
        $resultOk = $a->add($new_structure1);

        $new_structure2 = new structure_do();
        $new_structure2->setId("foo");
        $new_structure2->setName("Name foo");
        $new_structure2->setStorage('text/plain');
        $resultKo = $a->add($new_structure2);

        // Act
        $b = $a->getAllStructures();

        // Assert
        $this->assertEquals(1, count($b));
        $this->assertTrue($resultOk);
        $this->assertFalse($resultKo);

        /* Get structure */
        // Arrange
        $a = new structures_do();
        $a->load(DIR_BASE.'/test/data/structures_demo.json');

        // Act
        $b = $a->get('chat_tienda');
        
        // Assert
        $this->assertEquals($b->getId(), 'chat_tienda');
        $this->assertEquals($b->getName(), 'Chat de tienda online');
        $this->assertEquals($b->getStorage(), 'mongodb');

        // Act
        $b = $a->get('programa_tv');

        // Assert
        $this->assertEquals($b->getId(), 'programa_tv');
        $this->assertEquals($b->getName(), 'Programas TV');
        $this->assertEquals($b->getStorage(), 'text/plain');

        // Act
        $b = $a->get('no product');
        $this->assertNull($b);

        /* Delete structure */
        // Arrange
        $a = new structures_do();
        $a->load(DIR_BASE.'/test/data/structures_demo.json');
        $numInitialStructures = count($a->getAllStructures());

        // Act
        $result = $a->remove('chat_tienda');
        $b = $a->getAllStructures();

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(1, $numInitialStructures - count($b));

        // Arrange
        $a->load(DIR_BASE.'/test/data/structures_demo.json');
        $numInitialStructures = count($a->getAllStructures());

        // Act
        $result = $a->remove('no product');
        $b = $a->getAllStructures();

        // Assert
        $this->assertFalse($result);
        $this->assertEquals(0, $numInitialStructures - count($b));
    }

    // ...
}