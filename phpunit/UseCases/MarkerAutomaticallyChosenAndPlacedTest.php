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
class MarkerAutomaticallyChosenAndPlacedTest extends TestCase{
    protected ?MarkerAutomaticallyChosenAndPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateMarker $mock_update_marker = null;

    protected int $player_id = 77;

    protected array $current_data = [
        'tiles' => [77 => [22 => ['colour'=> 3]]],
        'candidate_tiles_for_marker' => [22 => ['horizontal' => 12, 'vertical' => 14, 'rotation' => 3, 'colour'=> 3]],
    ];
    protected array $marker_specification = ['horizontal' => 12, 'vertical' => 14, ];
    protected array $modified_marker_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14,];
    protected array $tile_specification = ['horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];
    protected array $modified_tile_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, 'colour'=> 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = MarkerAutomaticallyChosenAndPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_marker = $this->createMock(Infrastructure\UpdateMarker::class);
        $this->sut->set_update_marker($this->mock_update_marker);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_execute_moves_marker() {
        // Arrange
        $this->arrange();

        $this->mock_update_marker->expects($this->exactly(1))->method('move')->with($this->player_id, ['colour'=> 3]);
        // Act
        $this->act_default();
        // Assert
    }

    protected function arrange() {
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn($this->current_data);
        $this->mock_update_marker->expects($this->exactly(1))->method('calculate_location_argument')->with($this->modified_tile_specification)->willReturn(22);
        $this->mock_update_marker->expects($this->exactly(1))->method('get_marker')->with($this->player_id, ['colour'=> 3])->willReturn('x');
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
