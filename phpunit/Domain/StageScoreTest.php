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

    public function test_score_1_with_single_marker_single_location() {
        // Arrange
        $stage = new TopView();
        $location_1010 = ['horizontal' => 10, 'vertical' => 10];

        $stage->colour_map = [$this->get_key(10, 10) => 5];
        $stage->jewels = [$this->get_key(10, 10)];

        $this->sut->set_stage($stage);

        // Act
        $score = $this->sut->get_score([$location_1010]);
        // Assert
        $this->assertEquals($score, 2);
    }

    public function test_score_2_with_single_marker_double_jewel() {
        // Arrange
        $stage = new TopView();
        $location_1010 = ['horizontal' => 10, 'vertical' => 10];

        $stage->colour_map = [$this->get_key(10, 10) => 5, $this->get_key(10, 11) => 5];
        $stage->jewels = [$this->get_key(10, 10), $this->get_key(10, 11)];

        $this->sut->set_stage($stage);

        // Act
        $score = $this->sut->get_score([$location_1010]);
        // Assert
        $this->assertEquals($score, 4);
    }

    protected function get_key($horizontal, $vertical) {
        $location = ['horizontal' => $horizontal, 'vertical' => $vertical];
        return TopView::get_location_key($location);
    }
}
