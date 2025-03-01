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
        $this->assertEqualsCanonicalizing($expected_positions, $positions);
    }
    static public function adjacent_positions_provider(): array {

        $initial0 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $initial1 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial2 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial3 = ['horizontal' => 10, 'vertical' => 10, 'rotation' => 3];

        $initial01 = ['horizontal' => 12, 'vertical' => 10, 'rotation' => 0];

        $t8_8_0 = ['horizontal' => 8, 'vertical' => 8, 'rotation' => 0];
        $t10_8_0 = ['horizontal' => 10, 'vertical' => 8, 'rotation' => 0];
        $t12_8_0 = ['horizontal' => 12, 'vertical' => 8, 'rotation' => 0];
        $t6_10_0 = ['horizontal' => 6, 'vertical' => 10, 'rotation' => 0];
        $t14_10_0 = ['horizontal' => 14, 'vertical' => 10, 'rotation' => 0];
        $t8_12_0 = ['horizontal' => 8, 'vertical' => 12, 'rotation' => 0];
        $t10_12_0 = ['horizontal' => 10, 'vertical' => 12, 'rotation' => 0];
        $t12_12_0 = ['horizontal' => 12, 'vertical' => 12, 'rotation' => 0];

        $t10_8_0 = ['horizontal' => 10, 'vertical' => 8, 'rotation' => 0];

        return [
            [[], [$initial0, $initial1, $initial2, $initial3]],
            [[$initial0, $initial01], [
                $t8_8_0, $t10_8_0, $t12_8_0, $t6_10_0, $t14_10_0, $t8_12_0, $t10_12_0, $t12_12_0,
            ]],
            [[$initial0], [
                $t8_8_0, $t10_8_0, $initial01, $t6_10_0, $t8_12_0, $t10_12_0,
            ]],
        ];
    }
}
?>
