<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/AfterDominoPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class AfterDominoPlacedTest extends TestCase{
    protected ?AfterDominoPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateDomino $mock_update_domino = null;

    protected int $player_id = 77;

    protected array $current_data = ['current_stage' => 2, 'candidate_positions' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]]];
    protected array $domino_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = AfterDominoPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_domino = $this->createMock(Infrastructure\UpdateDomino::class);
        $this->sut->set_update_domino($this->mock_update_domino);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_transition_name_no_candidate_tiles() {
        // Arrange
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['candidate_tiles_for_marker' => []]);

        // Act
        $this->act_default();
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals($transition_name, 'no_candidate_tile');
    }

    public function test_transition_name_single_candidate_tile() {
        // Arrange
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['candidate_tiles_for_marker' => [1]]);

        // Act
        $this->act_default();
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals($transition_name, 'single_candidate_tile');
    }

    public function test_transition_name_double_candidate_tile() {
        // Arrange
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['candidate_tiles_for_marker' => [1, 2]]);

        // Act
        $this->act_default();
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals($transition_name, 'double_candidate_tile');
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
