<?php
namespace Bga\Games\Pyramido\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/MarkerChosenAndPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Domain/Colour.php');
use Bga\Games\Pyramido\Domain;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');
use Bga\Games\Pyramido\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class MarkerChosenAndPlacedTest extends TestCase{
    protected ?MarkerChosenAndPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateMarker $mock_update_marker = null;

    protected int $player_id = 77;

    protected array $current_data = [
        'tiles' => [77 => [1464 => ['colour' => 1]]],
        'players' => [77 => ['name'=> 'x']],
    ];
    protected array $marker_specification = ['horizontal' => 12, 'vertical' => 14, ];
    protected array $modified_marker_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14,];
    protected array $tile_specification = ['horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];
    protected array $updated_marker = ['colour' => 1];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = MarkerChosenAndPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_marker = $this->createMock(Infrastructure\UpdateMarker::class);
        $this->sut->set_update_marker($this->mock_update_marker);

        $this->sut->set_tile_specification($this->tile_specification);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_execute_moves_marker() {
        // Arrange
        $this->arrange();

        $this->mock_update_marker->expects($this->exactly(1))->method('move')->with($this->player_id, $this->updated_marker);
        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_notifies_players() {
        // Arrange
        $this->arrange();

        $this->mock_notifications->expects($this->exactly(1))->method('notifyAllPlayers')
        ->with('marker_placed', '${player_name} places ${colour} marker', 
        [ 'player_id' => $this->player_id,
          'player_name' => 'x',
          'marker_specification' => $this->updated_marker,
          'colour' => Domain\COLOURS[$this->updated_marker['colour']],
        ]);
     
        // Act
        $this->act_default();
        // Assert
    }

    protected function arrange() {
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn($this->current_data);
        $this->mock_update_marker->expects($this->exactly(1))->method('get_marker')->with($this->player_id, $this->updated_marker)->willReturn($this->updated_marker);
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
