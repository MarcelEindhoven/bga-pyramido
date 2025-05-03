<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/CheckResurfacing.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class CheckResurfacingTest extends TestCase{
    protected ?CheckResurfacing $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;

    protected int $player_id = 77;

    protected array $current_data = ['current_stage' => 2, 'resurfacings' => [77 => []]];
    protected array $domino_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = CheckResurfacing::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_notify_no_candidate_tiles() {
        // Arrange
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn($this->current_data);
        $this->mock_notifications->expects($this->exactly(1))->method('notifyPlayer')
        ->with($this->player_id, 'candidate_tiles_for_resurfacing', '', ['candidate_tiles_for_resurfacing' => []]);

        // Act
        $this->act_default();
        // Assert
    }

    public function test_notify_candidate_tiles() {
        // Arrange
        $this->current_data['resurfacings'][$this->player_id] = [1];
        $this->current_data['candidate_tiles_for_resurfacing'] = ['tile'];
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn($this->current_data);

        $this->mock_notifications->expects($this->exactly(1))->method('notifyPlayer')
        ->with($this->player_id, 'candidate_tiles_for_resurfacing', '',
        ['candidate_tiles_for_resurfacing' => ['tile']]);

        // Act
        $this->act_default();
        // Assert
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
