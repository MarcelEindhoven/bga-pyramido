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
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_marker, $this->default_marker]);

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
        $this->sut->set_players($this->players);
        $this->mock_cards->expects($this->exactly(2))->method('getCardsInLocation')->willReturn(
            [$this->default_marker, $this->default_marker, $this->default_marker]);

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

    protected function act_default() {
        return $this->sut->get()[$this->player_id];
    }
}
?>
