<?php
namespace Bga\Games\Pyramido\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/Pyramid.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

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
        print("test_resurfacing_replaces_tile\n");
        // Arrange
        $tiles = [Pyramid::get_location_key($this->initial41010) => $this->initial41010];
        $this->sut = Pyramid::create($tiles);

        // Act
        $this->sut->resurface([$this->resurfacing]);

        // Assert
        $this->assertEquals([Pyramid::get_location_key($this->initial41010) => $this->resurfacing], $this->sut->get_tiles());
    }

    public function test_resurfacing_replaces_tile_horizontal() {
        print("test_resurfacing_replaces_tile_horizontal\n");
        // Arrange
        $initial41210 = $this->initial41010;
        $initial41210['horizontal'] = 12;
        $tiles = [Pyramid::get_location_key($initial41210) => $initial41210,
        Pyramid::get_location_key($this->initial41010) => $this->initial41010];
        $this->sut = Pyramid::create($tiles);

        // Act
        $this->sut->resurface([$this->resurfacing]);

        // Assert
        $expected_tiles = [Pyramid::get_location_key($initial41210) => $initial41210,
        Pyramid::get_location_key($this->initial41010) => $this->resurfacing];
        $this->assertEquals($expected_tiles, $this->sut->get_tiles());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_stage_next_domino_provider')]
    public function test_current_stage($tiles, $expected_stage) {
        print("test_current_stage\n");
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
        print("test_get_candidate_tiles_for_resurfacing\n");
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
        print("test_get_candidate_tiles_for_marker\n");
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

    #[\PHPUnit\Framework\Attributes\DataProvider('adjacent_positions_provider_stage')]
    public function test_get_candidate_positions_stage($stage, $tiles, $expected_positions) {
        print("test_get_candidate_positions_stage\n");
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
