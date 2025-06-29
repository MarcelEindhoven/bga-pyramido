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

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class PyramidTest extends TestCase{
    protected ?Pyramid $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected array $initial41010 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 3, 'jewels' => []];
    protected array $resurfacing = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2, 'jewels' => [1]];

    protected function setUp(): void {
    }

    public function test_resurfacing_replaces_tile() {
        // Arrange
        $tiles = [Infrastructure\CurrentTiles::calculate_array_index($this->initial41010) => $this->initial41010];
        $this->sut = Pyramid::create($tiles);

        // Act
        $this->sut->resurface([$this->resurfacing]);

        // Assert
        $this->assertEquals([Infrastructure\CurrentTiles::calculate_array_index($this->initial41010) => $this->resurfacing], $this->sut->get_tiles());
    }

    public function test_resurfacing_replaces_tile_horizontal() {
        // Arrange
        $initial41210 = $this->initial41010;
        $initial41210['horizontal'] = 12;
        $tiles = [Infrastructure\CurrentTiles::calculate_array_index($initial41210) => $initial41210,
        Infrastructure\CurrentTiles::calculate_array_index($this->initial41010) => $this->initial41010];
        $this->sut = Pyramid::create($tiles);

        // Act
        $this->sut->resurface([$this->resurfacing]);

        // Assert
        $expected_tiles = [Infrastructure\CurrentTiles::calculate_array_index($initial41210) => $initial41210,
        Infrastructure\CurrentTiles::calculate_array_index($this->initial41010) => $this->resurfacing];
        $this->assertEquals($expected_tiles, $this->sut->get_tiles());
    }

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
        $stage4_0 = ['stage' => 4,];
        $stage3_0 = ['stage' => 3,];
        $stage2_0 = ['stage' => 2,];
        $stage1_0 = ['stage' => 1,];
        return [
            [[], 1],
            [[$stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
                ], 2],
            [[$stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
                ], 1],
            [[$stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, 
            $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage3_0, $stage3_0], 3],
            [[$stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, 
            $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage4_0, $stage4_0], 2],
            [[$stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0], 4],
            [[$stage4_0, $stage4_0], 1],
            [[$stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, $stage1_0, 
            $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, 
            $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0, $stage2_0,
            $stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0, $stage3_0,
            $stage4_0, $stage4_0], 5],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_candidate_tiles_for_resurfacing')]
    public function test_get_candidate_tiles_for_resurfacing($tiles, $markers, $expected_tiles) {
        // Arrange
        $this->sut = Pyramid::create($tiles);

        // Act
        $candidate_tiles_for_resurfacing = $this->sut->get_candidate_tiles_for_resurfacing($markers);

        // Assert
        $this->assertEquals($expected_tiles, $candidate_tiles_for_resurfacing);
    }
    static public function get_candidate_tiles_for_resurfacing(): array {
        $initial1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 1];
        $initial4 = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 2];
        $initial4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 3];
        $marker4 = ['stage' => 4, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 0];
        $marker4b = ['stage' => 4, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 0];
        return [
            [[], [], []], // No tiles, no candidate
            [[$initial1], [], []], // Only stage 4 is a candidate
            [[$initial4], [], [$initial4]], // Only stage 4 is a candidate
            [[$initial4, $initial4b, $initial1], [], [$initial4, $initial4b]], // Only stage 4 is a candidate
            [[$initial4, $initial4b, $initial1], [$marker4], [$initial4]], // Tile with marker is not a candidate
            [[$initial4, $initial4b, $initial1], [$marker4b], [$initial4, $initial4b]], // Tile with marker is not a candidate
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_candidate_tiles_for_marker')]
    public function test_get_candidate_tiles_for_marker($tiles, $markers, $expected_tiles) {
        // Arrange
        $this->sut = Pyramid::create($tiles);

        // Act
        $candidate_tiles_for_marker = $this->sut->get_candidate_tiles_for_marker($markers);

        // Assert
        $this->assertEquals($expected_tiles, $candidate_tiles_for_marker);
    }
    static public function get_candidate_tiles_for_marker(): array {
        $stage4_5 = ['stage' => 4, 'colour' => 5, 'jewels' => [1]];
        $stage4_5no_jewels = ['stage' => 4, 'colour' => 5, 'jewels' => []];
        $stage4_4 = ['stage' => 4, 'colour' => 4, 'jewels' => [1]];
        $stage2_0 = ['stage' => 2, 'colour' => 3, 'jewels' => [1]];
        $stage1_0 = ['stage' => 1, 'colour' => 2, 'jewels' => [1]];
        $marker_stage_0 = ['stage' => 0, 'colour' => 5,];
        $marker_stage_1_4 = ['stage' => 1, 'colour' => 4,];
        $marker_stage_4 = ['stage' => 4, 'colour' => 0,];
        $marker4_stage_0 = ['stage' => 0, 'colour' => 4,];
        return [
            [[], [], []],
            [[$stage4_5], [$marker_stage_0, $marker_stage_1_4, $marker_stage_4], [$stage4_5]],
            [[$stage4_5, $stage2_0], [$marker_stage_0, $marker_stage_4], [$stage4_5]],
            [[$stage4_5, $stage4_4], [$marker_stage_0, $marker_stage_4], [$stage4_5]],
            [[$stage4_5, $stage4_4], [$marker_stage_0, $marker_stage_1_4], [$stage4_5]],
            [[$stage4_5, $stage4_4], [$marker_stage_0, $marker4_stage_0], [$stage4_5, $stage4_4]],
            [[$stage4_5, $stage4_5], [$marker_stage_0], [$stage4_5, $stage4_5]],
            [[$stage4_5, $stage4_5no_jewels], [$marker_stage_0], [$stage4_5]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adjacent_positions_provider_first_stage')]
    public function test_get_adjacent_positions_first_stage($tiles, $expected_positions) {
        // Arrange
        $this->sut = Pyramid::create($tiles);

        // Act
        $positions = $this->sut->get_adjacent_positions_first_stage(1);
        // Assert
        // print_r(array_keys($positions));
        $this->assertEqualsCanonicalizing($expected_positions, $positions);
    }
    static public function adjacent_positions_provider_first_stage(): array {

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
        $t12_16_0 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 16, 'rotation' => 0];
        $t16_14_0 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 14, 'rotation' => 0];
        $t14_18_0 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 18, 'rotation' => 0];
        $t14_16_0 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 16, 'rotation' => 0];
        $t14_14_0 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 14, 'rotation' => 0];
        $t14_12_0 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 12, 'rotation' => 0];
        $t16_12_0 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 12, 'rotation' => 0];
        $t12_14_0 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 0];
        $t16_16_0 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 16, 'rotation' => 0];
        $t10_16_0 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 16, 'rotation' => 0];
        $t12_18_0 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 18, 'rotation' => 0];

        $t10_6_1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 6, 'rotation' => 1];
        $t12_6_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 6, 'rotation' => 1];
        $t12_8_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 1];
        $t8_8_1 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 8, 'rotation' => 1];
        $t8_10_1 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 10, 'rotation' => 1];
        $t14_8_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 8, 'rotation' => 1];
        $t14_10_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 1];
        $t10_12_1 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 12, 'rotation' => 1];
        $t12_12_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 1];
        $t12_10_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 1];
        $t18_12_1 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 12, 'rotation' => 1];
        $t14_14_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 14, 'rotation' => 1];
        $t16_14_1 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 14, 'rotation' => 1];
        $t18_14_1 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 14, 'rotation' => 1];
        $t16_12_1 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 12, 'rotation' => 1];
        $t14_16_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 16, 'rotation' => 1];
        $t14_12_1 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 12, 'rotation' => 1];
        $t16_16_1 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 16, 'rotation' => 1];
        $t16_10_1 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 10, 'rotation' => 1];
        $t12_16_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 16, 'rotation' => 1];
        $t12_14_1 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 1];

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
        $t16_14_2 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 14, 'rotation' => 2];
        $t16_18_2 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 18, 'rotation' => 2];
        $t14_16_2 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 16, 'rotation' => 2];
        $t16_12_2 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 12, 'rotation' => 2];
        $t18_16_2 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 16, 'rotation' => 2];
        $t18_12_2 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 12, 'rotation' => 2];
        $t14_14_2 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 14, 'rotation' => 2];
        $t14_18_2 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 18, 'rotation' => 2];
        $t12_16_2 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 16, 'rotation' => 2];

        $t10_8_3 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 8, 'rotation' => 3];
        $t12_8_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 8, 'rotation' => 3];
        $t12_10_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 10, 'rotation' => 3];
        $t8_10_3 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 10, 'rotation' => 3];
        $t8_12_3 = ['stage' => 1, 'horizontal' => 8, 'vertical' => 12, 'rotation' => 3];
        $t14_10_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 3];
        $t14_12_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 12, 'rotation' => 3];
        $t10_14_3 = ['stage' => 1, 'horizontal' => 10, 'vertical' => 14, 'rotation' => 3];
        $t12_14_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3];
        $t12_12_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 3];
        $t18_14_3 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 14, 'rotation' => 3];
        $t16_16_3 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 16, 'rotation' => 3];
        $t16_14_3 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 14, 'rotation' => 3];
        $t18_16_3 = ['stage' => 1, 'horizontal' => 18, 'vertical' => 16, 'rotation' => 3];
        $t14_16_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 16, 'rotation' => 3];
        $t14_18_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 18, 'rotation' => 3];
        $t16_12_3 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 12, 'rotation' => 3];
        $t16_18_3 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 18, 'rotation' => 3];
        $t14_14_3 = ['stage' => 1, 'horizontal' => 14, 'vertical' => 14, 'rotation' => 3];
        $t12_16_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 16, 'rotation' => 3];
        $t12_18_3 = ['stage' => 1, 'horizontal' => 12, 'vertical' => 18, 'rotation' => 3];

        $faraway_horizontal = ['stage' => 1, 'horizontal' => 18, 'vertical' => 16, 'rotation' => 3];
        $faraway_4x4 = ['stage' => 1, 'horizontal' => 16, 'vertical' => 16, 'rotation' => 3];

        $stage4_0 = ['stage' => 4, 'horizontal' => 13, 'vertical' => 13, 'rotation' => 0];
        $stage4_1 = ['stage' => 4, 'horizontal' => 13, 'vertical' => 13, 'rotation' => 1];
        $stage4_2 = ['stage' => 4, 'horizontal' => 15, 'vertical' => 13, 'rotation' => 2];
        $stage4_3 = ['stage' => 4, 'horizontal' => 13, 'vertical' => 15, 'rotation' => 3];

        return [
            [[], [$initial0, $initial1, $initial2, $initial3]],
            [[$initial0, $initial01], [
                $t8_8_0, $t10_8_0, $t12_8_0, $t6_10_0, $t14_10_0, $t8_12_0, $t10_12_0, $t12_12_0,
                $t10_6_1, $t12_6_1, $t8_8_1, $t8_10_1, $t14_8_1, $t14_10_1, $t10_12_1, $t12_12_1,
                $t10_8_2, $t12_8_2, $t14_8_2, $t8_10_2, $t16_10_2, $t10_12_2, $t12_12_2, $t14_12_2,
                $t10_8_3, $t12_8_3, $t8_10_3, $t8_12_3, $t14_10_3, $t14_12_3, $t10_14_3, $t12_14_3,
            ]],
            [[$initial0, $faraway_horizontal], [
                $t12_10_0, $t10_12_0,
                $t12_10_1, $t10_12_1,
                $t14_10_2, $t12_12_2,
                $t12_12_3, $t10_14_3,
                $t16_14_0, $t14_16_0,
                $t18_12_1, $t16_14_1,
                $t18_14_2, $t16_16_2,
                $t18_14_3, $t16_16_3,
            ]],
            [[$initial0, $faraway_4x4], [ // 4x5 and 5x4 allowed, so not the 8,8 and 18,18 corner
                $t10_8_0, $t8_12_0, // Dominoes allowed because of 10, 10 tile
                $t12_10_0, $t10_12_0,
                $t12_8_1, $t8_10_1, 
                $t12_10_1, $t10_12_1,
                $t12_8_2,  $t10_12_2,
                $t14_10_2, $t12_12_2,
                $t12_10_3, $t8_12_3, 
                $t12_12_3, $t10_14_3,
                $t14_14_0, $t12_16_0, // Dominoes allowed because of 16, 16 tile
                $t16_14_0, $t14_18_0,
                $t18_14_1, $t14_14_1,
                $t14_16_1, $t16_12_1,
                $t16_14_2, $t16_18_2,
                $t18_14_2, $t14_16_2,
                $t16_14_3, $t18_16_3,
                $t14_18_3, $t14_16_3,
            ]],
            [[$initial0, $t16_14_2, $t14_16_3,], [ // 4x5 and 5x4 allowed, so not the 8,8 corner
                $t10_8_0, $t8_12_0, // Dominoes allowed because of 10, 10 tile
                $t12_10_0, $t10_12_0,
                $t12_8_1, $t8_10_1, 
                $t12_10_1, $t10_12_1,
                $t12_8_2,  $t10_12_2,
                $t14_10_2, $t12_12_2,
                $t12_10_3, $t8_12_3, 
                $t12_12_3, $t10_14_3,
                $t14_12_0, $t12_14_0, // Dominoes allowed because of 16, 14 tile
                $t16_12_0, $t16_16_0,
                $t18_12_1, $t14_12_1,
                $t18_14_1, $t16_16_1, $t16_10_1,
                $t16_12_2, $t18_16_2,
                $t18_12_2, $t14_14_2,
                $t16_12_3, $t18_16_3, $t18_14_3,
                $t16_18_3, $t14_14_3,
                $t10_16_0, // Dominoes additionally allowed because of 14, 16 tile
                $t12_18_0, $t14_18_0,
                $t12_14_1, $t12_16_1,
                $t16_18_2,
                $t14_18_2, $t12_16_2,
                $t12_18_3, $t12_16_3,
            ]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adjacent_positions_provider_stage')]
    public function test_get_candidate_positions_stage($stage, $tiles, $expected_positions) {
        // Arrange
        $this->sut = Pyramid::create($tiles);

        // Act
        $positions = $this->sut->get_candidate_positions_stage($stage);
        // Assert
        $this->assertEqualsCanonicalizing($expected_positions, $positions);
    }
    static public function adjacent_positions_provider_stage(): array {

        $stage1_top_left = ['stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];

        $stage1_far_right = ['stage' => 1, 'horizontal' => 18, 'vertical' => 16, 'rotation' => 3];
        $stage1_far_bottom = ['stage' => 1, 'horizontal' => 16, 'vertical' => 18, 'rotation' => 3];

        $stage4_0 = ['stage' => 4, 'horizontal' => 13, 'vertical' => 13, 'rotation' => 0];
        $stage4_1 = ['stage' => 4, 'horizontal' => 13, 'vertical' => 13, 'rotation' => 1];
        $stage4_2 = ['stage' => 4, 'horizontal' => 15, 'vertical' => 13, 'rotation' => 2];
        $stage4_3 = ['stage' => 4, 'horizontal' => 13, 'vertical' => 15, 'rotation' => 3];

        $stage3_0_00 = ['stage' => 3, 'horizontal' => 12, 'vertical' => 12, 'rotation' => 0];
        $stage3_0_10 = ['stage' => 3, 'horizontal' => 14, 'vertical' => 12, 'rotation' => 0];
        $stage3_0_01 = ['stage' => 3, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 0];
        $stage3_0_11 = ['stage' => 3, 'horizontal' => 14, 'vertical' => 14, 'rotation' => 0];
        $stage3_0_02 = ['stage' => 3, 'horizontal' => 12, 'vertical' => 16, 'rotation' => 0];
        $stage3_2_12 = ['stage' => 3, 'horizontal' => 14, 'vertical' => 16, 'rotation' => 2];

        return [
            [4, [$stage1_top_left, $stage1_far_right], [
                $stage4_0, $stage4_2,
            ]],
            [4, [$stage1_top_left, $stage1_far_bottom], [
                $stage4_1, $stage4_3,
            ]],
            [3, [$stage1_top_left, $stage1_far_bottom
            , $stage3_0_00, $stage3_0_10, $stage3_0_01, $stage3_0_11], [
                $stage3_0_02, $stage3_2_12,
            ]],
        ];
    }
}
?>
