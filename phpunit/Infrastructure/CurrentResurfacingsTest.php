<?php
namespace Bga\Games\PyramidoCannonFodder\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Resurfacing.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class CurrentResurfacingsTest extends TestCase{
    protected ?CurrentResurfacings $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected string $player_id = '77';
    protected array $players = ['7' => [], '77' => [],];
    protected array $default_resurfacing = ['id' => 0, 'type' => 0, 'type_arg' => 1, 'location' => '0', 'location_arg' => 0];

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = CurrentResurfacings::create($this->mock_cards);
    }

    public function test_get_placed_resurfacings_for_2players() {
        // Arrange
        $this->sut->set_players($this->players);

        // Act
        $placed_resurfacings = $this->sut->get_placed_resurfacings();
        // Assert
    }

    public function test_get_resurfacings_for_player() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $resurfacing2 = $this->default_resurfacing;
        $resurfacing2['type_arg'] = 5;
        $resurfacing2['type'] = 4;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_resurfacing, $resurfacing2]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals(count($resurfacings), 2 * 2);
    }

    public function test_get_resurfacings_for_2players() {
        // Arrange
        $this->sut->set_players($this->players);
        $this->mock_cards->expects($this->exactly(2))->method('getCardsInLocation')->willReturn(
            [$this->default_resurfacing, $this->default_resurfacing, $this->default_resurfacing]);

        // Act
        $resurfacings = $this->sut->get();
        // Assert
        $this->assertEquals(count($resurfacings), 2);
    }

    public function test_get_3resurfacings_per_player() {
        // Arrange
        $resurfacing2 = $this->default_resurfacing;
        $resurfacing2['type'] = 4;
        $resurfacing2['type_arg'] = 5;
        $resurfacing3 = $this->default_resurfacing;
        $resurfacing3['type'] = 2;
        $resurfacing3['type_arg'] = 3;
        $this->sut->set_players($this->players);
        $this->mock_cards->expects($this->exactly(2))->method('getCardsInLocation')->willReturn(
            [$this->default_resurfacing, $resurfacing2, $resurfacing3]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals(count($resurfacings), 2 * 3);
    }

    public function test_get_first_colour() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $colour = 3;
        $this->default_resurfacing['type'] = $colour;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_resurfacing]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals($resurfacings[3]['colour'], $colour);
    }

    public function test_get_first_unique_id() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $id = 3;
        $this->default_resurfacing['id'] = $id;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_resurfacing]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals($resurfacings[0]['id'], $id);
    }

    public function test_get_second_colour() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $colour = 5;
        $this->default_resurfacing['type_arg'] = $colour;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_resurfacing]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals($resurfacings[$colour]['colour'], $colour);
    }

    public function test_get_second_unique_id() {
        // Arrange
        $this->sut->set_players(['77' => [],]);
        $id = 3;
        $this->default_resurfacing['id'] = $id;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_resurfacing]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals($resurfacings[1]['id'], $id + 100);
    }

    public function test_get_stage_default_0() {
        // Arrange
        $this->sut->set_players([77 => [],]);
        $stage = 0;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn(
            [$this->default_resurfacing]);

        // Act
        $resurfacings = $this->act_default();
        // Assert
        $this->assertEquals($resurfacings[0]['stage'], $stage);
        $this->assertEquals($resurfacings[1]['stage'], $stage);
    }

    protected function act_default() {
        return $this->sut->get()[$this->player_id];
    }
}
?>
