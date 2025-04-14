<?php
namespace Bga\Games\PyramidoCannonFodder\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class CurrentMarkersTest extends TestCase{
    protected ?CurrentMarkers $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected string $player_id = '77';
    protected array $players = ['7' => [], '77' => [],];
    protected array $default_marker = ['id' => 0, 'type' => 0, 'type_arg' => 0, 'location' => '0', 'location_arg' => 0];

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = CurrentMarkers::create($this->mock_cards);
    }

    public function test_get_markers_for_player() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $marker2 = $this->default_marker;
        $marker2['type'] = 5;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_marker, $marker2]);

        // Act
        $markers = $this->act_default();
        // Assert
        $this->assertEquals(count($markers), 2);
    }

    public function test_get_markers_for_2players() {
        // Arrange
        $this->sut->set_players($this->players);
        $this->mock_cards->expects($this->exactly(2))->method('getCardsInLocation')->willReturn(
            [$this->default_marker, $this->default_marker, $this->default_marker]);

        // Act
        $markers = $this->sut->get();
        // Assert
        $this->assertEquals(count($markers), 2);
    }

    public function test_get_3markers_per_player() {
        // Arrange
        $marker2 = $this->default_marker;
        $marker2['type'] = 2;
        $marker3 = $this->default_marker;
        $marker3['type'] = 3;
        $this->sut->set_players($this->players);
        $this->mock_cards->expects($this->exactly(2))->method('getCardsInLocation')->willReturn(
            [$this->default_marker, $marker2, $marker3]);

        // Act
        $markers = $this->act_default();
        // Assert
        $this->assertEquals(count($markers), 3);
    }

    public function test_get_colour() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $colour = 3;
        $this->default_marker['type'] = $colour;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_marker]);

        // Act
        $markers = $this->act_default();
        // Assert
        $this->assertEquals(reset($markers)['colour'], $colour);
    }

    public function test_get_unique_id() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $id = 3;
        $this->default_marker['id'] = $id;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_marker]);

        // Act
        $markers = $this->act_default();
        // Assert
        $this->assertEquals(reset($markers)['id'], $id);
    }

    public function test_get_stage_default_0() {
        // Arrange
        $this->sut->set_players([77 => [],]);
        $stage = 0;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_marker]);

        // Act
        $markers = $this->act_default();
        // Assert
        $this->assertEquals(reset($markers)['stage'], $stage);
    }

    public function test_get_pyramid_location() {
        // Arrange
        $this->sut->set_players(['77' => [],]);

        $stage = 3;
        $horizontal = 10;
        $vertical = 12;
        $this->default_marker['location_arg'] = $stage
         + $horizontal * CurrentTiles::FACTOR_STAGE
          + $vertical * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL;

        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_marker]);

        // Act
        $markers = $this->act_default();
        $marker = reset($markers);
        // Assert
        $this->assertEquals($marker['stage'], $stage);
        $this->assertEquals($marker['horizontal'], $horizontal);
        $this->assertEquals($marker['vertical'], $vertical);
    }

    protected function act_default() {
        return $this->sut->get()[$this->player_id];
    }
}
?>
