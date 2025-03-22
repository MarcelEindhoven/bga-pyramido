<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/AIDominoChosenAndPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Domain/Pyramid.php');
use Bga\Games\PyramidoCannonFodder\Domain;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class AIDominoChosenAndPlacedTest extends TestCase{
    protected ?AIDominoChosenAndPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateDomino $mock_update_domino = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';

    protected array $current_data = ['candidate_positions' => []];
    protected array $expected_domino_specification = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = AIDominoChosenAndPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_domino = $this->createMock(Infrastructure\UpdateDomino::class);
        $this->sut->set_update_domino($this->mock_update_domino);

        $this->sut->set_player_id($this->player_id);
        $this->current_data['candidate_positions'] = Domain\Pyramid::get_adjacent_positions_first_stage_initial();
    }

    public function test_execute_choose_zero_rotation() {
        // Arrange
        $this->arrange();

        $this->mock_update_domino->expects($this->exactly(1))->method('move')->with($this->quarry_index, $this->player_id, $this->expected_domino_specification);
        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_choose_next_domino() {
        // Arrange
        $initial_right = ['horizontal' => 12, 'vertical' => 10, 'rotation' => 0];
        $pyramid = Domain\Pyramid::create([$initial_right]);
        $this->current_data['candidate_positions'] = $pyramid->get_adjacent_positions_first_stage();
        $this->expected_domino_specification = ['stage' => 4, 'horizontal' => 14, 'vertical' => 10, 'rotation' => 0, ];
        $this->arrange();

        $this->mock_update_domino->expects($this->exactly(1))->method('move')->with($this->quarry_index, $this->player_id, $this->expected_domino_specification);
        // Act
        $this->act_default();
        // Assert
    }

    protected function arrange() {
        $this->mock_update_domino->expects($this->exactly(1))->method('get_domino')->with($this->player_id, $this->expected_domino_specification)->willReturn('x');
        $this->mock_update_domino->expects($this->exactly(1))->method('get_first_tile_for')->with('x')->willReturn('a');
        $this->mock_update_domino->expects($this->exactly(1))->method('get_second_tile_for')->with('x')->willReturn('b');
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn($this->current_data);
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
