<?php
namespace Bga\Games\Pyramido\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/NextPlayer.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\Pyramido\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class NextPlayerTest extends TestCase{
    protected ?NextPlayer $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';

    protected array $current_data = [
        'candidate_positions' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]],
        'candidate_tiles_for_marker' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]]
    ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = NextPlayer::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_execute_notifies_player() {
        // Arrange
        $this->arrange();

        $this->mock_notifications->expects($this->exactly(1))->method('notifyPlayer');
        // Act
        $this->act_default();
        // Assert
    }
    protected function arrange() {
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn($this->current_data);
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
