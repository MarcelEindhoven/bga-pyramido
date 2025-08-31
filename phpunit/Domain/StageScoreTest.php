<?php
namespace Bga\Games\PyramidoCannonFodder\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/StageScore.php');
include_once(__DIR__.'/../../export/modules/php/Domain/TopView.php');

class StageScoreTest extends TestCase {
    protected ?StageScore $sut = null;
    protected array $marker_1110 = ['horizontal' => 11, 'vertical' => 10, 'colour' => 5];
    protected array $location_1110 = ['horizontal' => 11, 'vertical' => 10];
    protected array $marker_1010 = ['horizontal' => 10, 'vertical' => 10, 'colour' => 0];
    protected array $location_1010 = ['horizontal' => 10, 'vertical' => 10];
    protected array $location_1011 = ['horizontal' => 10, 'vertical' => 11];

    protected function setUp(): void {
        $this->sut = new StageScore();
    }

    public function test_score_empty_without_markers() {
        // Arrange

        // Act
        $score = $this->sut->get_score([]);
        // Assert
        $this->assertEquals($score, 0);
    }

    public function test_score_2x1_with_single_marker_single_location() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 5];
        $jewels = [$this->get_key(10, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010]);
        // Assert
        $this->assertEquals($score, 2);
    }

    public function test_score_2x2_with_single_marker_double_jewel() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 5, $this->get_key(10, 11) => 5];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 11)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010]);
        // Assert
        $this->assertEquals($score, 4);
    }

    public function test_score_2x1_with_single_marker_double_jewel_double_colour() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 5, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 11)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010]);
        // Assert
        $this->assertEquals($score, 2);
    }

    public function test_score_2x1_with_single_marker_separated_locations_down() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(10, 11) => 5, $this->get_key(10, 12) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 12)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010]);
        // Assert
        $this->assertEquals($score, 2);
    }

    public function test_score_2x2_with_single_marker_horizontal_area() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 4, $this->get_key(10, 12) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010]);
        // Assert
        $this->assertEquals($score, 4);
    }

    public function test_score_2x2_with_single_marker_multiple_initial_neighbours() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 4, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 11), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010]);
        // Assert
        $this->assertEquals($score, 4);
    }

    public function test_score_2x1p1_with_double_marker() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 1, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 11), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010, $this->marker_1110]);
        // Assert
        $this->assertEquals($score, 3);
    }

    public function test_score_2x1p2_with_double_marker() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 1, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 11), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1010, $this->marker_1110]);
        // Assert
        $this->assertEquals($score, 4);
    }

    public function test_score_2x1p2_with_double_marker_reverse_order() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 1, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 11), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score = $this->sut->get_score([$this->marker_1110, $this->marker_1010]);
        // Assert
        $this->assertEquals($score, 4);
    }

    public function test_get_score_details_contains_score() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 1, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 11), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score_details = $this->sut->get_score_details([$this->marker_1110, $this->marker_1010]);
        // Assert
        $this->assertEquals($score_details['score'], 4);
    }

    public function test_get_score_details_contains_jewels_first_marker() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 1, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score_details = $this->sut->get_score_details([$this->marker_1110]);
        // Assert
        $this->assertEquals($score_details['jewels_per_marker_sorted'], [$this->marker_1110['colour'] => [$this->location_1110]]);
    }

    public function test_get_score_details_2x1p2_with_double_marker() {
        // Arrange
        $colour_map = [$this->get_key(10, 10) => 4, $this->get_key(11, 10) => 1, $this->get_key(10, 11) => 4];
        $jewels = [$this->get_key(10, 10), $this->get_key(10, 11), $this->get_key(11, 10)];

        $this->sut->set_stage($jewels, $colour_map);

        // Act
        $score_details = $this->sut->get_score_details([$this->marker_1010, $this->marker_1110]);
        // Assert
        $this->assertEquals(
            $score_details['jewels_per_marker_sorted'], [
                $this->marker_1110['colour'] => [2 => $this->location_1110],
                $this->marker_1010['colour'] => [$this->location_1010, $this->location_1011],
                    ]);
    }

    protected function get_key($horizontal, $vertical) {
        $location = ['horizontal' => $horizontal, 'vertical' => $vertical];
        return TopView::get_location_key($location);
    }
}
