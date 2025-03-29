<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/MarkerChosenAndPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

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
        'candidate_positions' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]],
        'candidate_tiles_for_marker' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]]
    ];
    protected array $marker_specification = ['stage' => 2, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];
    protected array $modified_marker_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = MarkerChosenAndPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_marker = $this->createMock(Infrastructure\UpdateMarker::class);
        $this->sut->set_update_marker($this->mock_update_marker);

        $this->sut->set_marker_specification($this->marker_specification);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_execute_moves_marker() {
        // Arrange
        $this->arrange();

        $this->mock_update_marker->expects($this->exactly(1))->method('move')->with($this->player_id, $this->modified_marker_specification);
        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_notifies_players() {
        // Arrange
        $this->arrange();

        $this->mock_notifications->expects($this->exactly(1))->method('notifyAllPlayers')
        ->with('marker_placed', 'marker_placed', 
        [ 'player_id' => $this->player_id
        , 'marker_specification' => 'x'
        ]);
        // Act
        $this->act_default();
        // Assert
    }

    protected function arrange() {
        $this->mock_update_marker->expects($this->exactly(1))->method('get_marker')->with($this->player_id, $this->modified_marker_specification)->willReturn('x');
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
