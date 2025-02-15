<?php
namespace Bga\Games\PyramidoCannonFodder\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/Pyramid.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class PyramidTest extends TestCase{
    protected ?Pyramid $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected function setUp(): void {
    }

    /**
     * @dataProvider adjacent_positions_provider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('adjacent_positions_provider')]
    public function test_get_adjacent_positions($tiles, $expected_positions) {
        // Arrange
        $this->sut = Pyramid::create($tiles);

        // Act
        $positions = $this->sut->get_adjacent_positions_first_stage(1);
        // Assert
        $this->assertEquals($expected_positions, $positions);
    }
    static public function adjacent_positions_provider(): array {

        $initial0 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $initial1 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial2 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial3 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 3];

        return [
            [[], [$initial0, $initial1, $initial2, $initial3]],
        ];
    }
}
?>
