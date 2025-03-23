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
    #[\PHPUnit\Framework\Attributes\DataProvider('get_stage_next_domino_provider')]
    public function test_current_stage($tiles, $expected_stage) {
        // Arrange
        $this->sut = Pyramid::create($tiles);

        // Act
        $stage = $this->sut->get_stage_next_domino();

        // Assert
        $this->assertEquals($expected_stage, $stage);
    }
    static public function get_stage_next_domino_provider(): array {
        $stage3_0 = ['stage' => 3,];
        $stage2_0 = ['stage' => 2,];
        $stage1_0 = ['stage' => 1,];
        return [
            [[], 1],
            [[$stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
                ], 2],
            [[$stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, 
            $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage3_0, $stage3_0], 3],
            [[$stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0], 4],
        ];
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

        $initial0 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $initial1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial2 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial3 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 3];

        $initial01 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0];

        $t8_8_0 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 8, 'rotation' => 0];
        $t10_8_0 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 8, 'rotation' => 0];
        $t12_8_0 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 0];
        $t6_10_0 = ['stage' => 1, 'horizontal' => 6, 'vertical' => 10, 'rotation' => 0];
        $t14_10_0 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 0];
        $t8_12_0 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 12, 'rotation' => 0];
        $t10_12_0 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 12, 'rotation' => 0];
        $t12_12_0 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 0];
        $t12_10_0 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0];
        $t16_14_0 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 14, 'rotation' => 0];
        $t14_16_0 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 16, 'rotation' => 0];

        $t10_6_1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 6, 'rotation' => 1];
        $t12_6_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 6, 'rotation' => 1];
        $t8_8_1 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 8, 'rotation' => 1];
        $t8_10_1 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 10, 'rotation' => 1];
        $t14_8_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 8, 'rotation' => 1];
        $t14_10_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 1];
        $t10_12_1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 12, 'rotation' => 1];
        $t12_12_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 1];
        $t12_10_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 1];
        $t18_12_1 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 12, 'rotation' => 1];
        $t16_14_1 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 14, 'rotation' => 1];

        $t10_8_2 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 8, 'rotation' => 2];
        $t12_8_2 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 2];
        $t14_8_2 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 8, 'rotation' => 2];
        $t8_10_2 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 10, 'rotation' => 2];
        $t16_10_2 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 10, 'rotation' => 2];
        $t10_12_2 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 12, 'rotation' => 2];
        $t12_12_2 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 2];
        $t14_12_2 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 12, 'rotation' => 2];
        $t14_10_2 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 2];
        $t18_14_2 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 14, 'rotation' => 2];
        $t16_16_2 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 16, 'rotation' => 2];

        $t10_8_3 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 8, 'rotation' => 3];
        $t12_8_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 3];
        $t8_10_3 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 10, 'rotation' => 3];
        $t8_12_3 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 12, 'rotation' => 3];
        $t14_10_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 3];
        $t14_12_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 12, 'rotation' => 3];
        $t10_14_3 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 14, 'rotation' => 3];
        $t12_14_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3];
        $t12_12_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 3];
        $t18_14_3 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 14, 'rotation' => 3];
        $t16_16_3 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 16, 'rotation' => 3];

        $faraway = ['stage' => 1, 'horizontal' => 18, 'vertical' => 16, 'rotation' => 3];

        return [
            [[], [$initial0, $initial1, $initial2, $initial3]],
            [[$initial0, $initial01], [
                $t8_8_0, $t10_8_0, $t12_8_0, $t6_10_0, $t14_10_0, $t8_12_0, $t10_12_0, $t12_12_0,
                $t10_6_1, $t12_6_1, $t8_8_1, $t8_10_1, $t14_8_1, $t14_10_1, $t10_12_1, $t12_12_1,
                $t10_8_2, $t12_8_2, $t14_8_2, $t8_10_2, $t16_10_2, $t10_12_2, $t12_12_2, $t14_12_2,
                $t10_8_3, $t12_8_3, $t8_10_3, $t8_12_3, $t14_10_3, $t14_12_3, $t10_14_3, $t12_14_3,
            ]],
            [[$initial0, $faraway], [
                $t12_10_0, $t10_12_0,
                $t12_10_1, $t10_12_1,
                $t14_10_2, $t12_12_2,
                $t12_12_3, $t10_14_3,
                $t16_14_0, $t14_16_0,
                $t18_12_1, $t16_14_1,
                $t18_14_2, $t16_16_2,
                $t18_14_3, $t16_16_3,
            ]],
        ];
    }
}
?>
