<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/DominoChosenAndPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class DominoChosenAndPlacedTest extends TestCase{
    protected ?DominoChosenAndPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateDomino $mock_update_domino = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';

    protected array $current_data = ['candidate_positions' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]]];
    protected array $domino_specification = ['stage' => 2, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];
    protected array $modified_domino_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = DominoChosenAndPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_domino = $this->createMock(Infrastructure\UpdateDomino::class);
        $this->sut->set_update_domino($this->mock_update_domino);

        $this->sut->set_quarry_index($this->quarry_index);
        $this->sut->set_domino_specification($this->domino_specification);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_execute_moves_domino() {
        // Arrange
        $this->arrange();

        $this->mock_update_domino->expects($this->exactly(1))->method('move')->with($this->quarry_index, $this->player_id, $this->modified_domino_specification);
        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_notifies_players() {
        // Arrange
        $this->arrange();

        $this->mock_notifications->expects($this->exactly(1))->method('notifyAllPlayers')
        ->with('domino_placed', 'domino_placed', 
        ['quarry_index' => $this->quarry_index
        , 'player_id' => $this->player_id
        , 'tiles' => ['a', 'b']
        ]);
        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_notifies_player() {
        // Arrange
        $this->arrange();

        $this->mock_notifications->expects($this->exactly(1))->method('notifyPlayer')
        ->with($this->player_id, 'candidate_positions', '', 
        ['candidate_positions' => $this->current_data['candidate_positions']
        ]);
        // Act
        $this->act_default();
        // Assert
    }
    protected function arrange() {
        $this->mock_update_domino->expects($this->exactly(1))->method('get_domino')->with($this->player_id, $this->modified_domino_specification)->willReturn('x');
        $this->mock_update_domino->expects($this->exactly(1))->method('get_first_tile_for')->with('x')->willReturn('a');
        $this->mock_update_domino->expects($this->exactly(1))->method('get_second_tile_for')->with('x')->willReturn('b');
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn($this->current_data);
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
