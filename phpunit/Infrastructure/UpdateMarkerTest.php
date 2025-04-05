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

class UpdateMarkerTest extends TestCase{
    protected ?UpdateMarker $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';
    protected int $stage = 4;
    protected int $horizontal = 19;
    protected int $vertical = 18;
    protected int $rotation = 3;
    protected array $tile_specification = ['stage' => 0, 'horizontal' => 19, 'vertical' => 18, 'rotation' => 3, 'colour' => 4];
    protected array $card2 = ['id' => 1, 'type' => 2, 'type_arg' => 0, 'location' => '2371152', 'location_arg' => 0];
    protected array $card3 = ['id' => 3, 'type' => 3, 'type_arg' => 0, 'location' => '2371152', 'location_arg' => 999];
    protected array $card4 = ['id' => 2, 'type' => 4, 'type_arg' => 0, 'location' => '2371152', 'location_arg' => 0];

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = UpdateMarker::create($this->mock_cards);
    }

    public function test_move_single_card_matches_colour() {
        // Arrange
        $this->tile_specification['stage'] = 3;
        $this->tile_specification['colour'] = 2;

        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')
        ->with('77', 0)->willReturn([$this->card2]);

        $this->mock_cards->expects($this->exactly(1))->method('moveCard')
        ->with(1, '77'
        , $this->tile_specification['stage'] + 5 * $this->tile_specification['horizontal'] + 5*20* $this->tile_specification['vertical'] );

        // Act
        $this->sut->move($this->player_id, $this->tile_specification);
        // Assert
    }

    public function test_move_single_card_matches_colour_multiple_cards() {
        // Arrange
        $this->tile_specification['stage'] = 4;

        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')
        ->with('77', 0)->willReturn([$this->card2, $this->card4, $this->card3]);

        $this->mock_cards->expects($this->exactly(1))->method('moveCard')
        ->with(2, '77'
        , $this->tile_specification['stage'] + 5 * $this->tile_specification['horizontal'] + 5*20* $this->tile_specification['vertical'] );

        // Act
        $this->sut->move($this->player_id, $this->tile_specification);
        // Assert
    }
}
?>
