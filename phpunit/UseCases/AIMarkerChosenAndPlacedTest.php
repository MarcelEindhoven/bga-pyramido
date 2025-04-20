<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/AIMarkerChosenAndPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Domain/Pyramid.php');
use Bga\Games\PyramidoCannonFodder\Domain;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class AIMarkerChosenAndPlacedTest extends TestCase{
    protected ?AIMarkerChosenAndPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateMarker $mock_update_marker = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';

    protected array $current_data = ['tiles' => [77 => ['location_argument' => ['tile']]], 'candidate_tiles_for_marker' => []];
    protected array $expected_marker_specification = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = AIMarkerChosenAndPlaced::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_marker = $this->createMock(Infrastructure\UpdateMarker::class);
        $this->sut->set_update_marker($this->mock_update_marker);

        $this->sut->set_player_id($this->player_id);
    }

    public function test_execute_no_candidate_tiles() {
        // Arrange
        $this->current_data['candidate_tiles_for_marker'] = [];
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn($this->current_data);
        $this->arrange();

        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_single_candidate_tile() {
        // Arrange
        $this->current_data['candidate_tiles_for_marker'] = [['test' => 1, ]];
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn($this->current_data);

        $expected_tile_specification = ['test' => 1, 'stage' => 4];
        $this->mock_update_marker->expects($this->exactly(1))->method('calculate_location_argument')
        ->with($expected_tile_specification)->willReturn('location_argument');
        $this->arrange();

        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_double_candidate_tiles() {
        // Arrange
        $this->current_data['candidate_tiles_for_marker'] = [['test' => 1, ], ['test' => 1, ], ];
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn($this->current_data);

        $expected_tile_specification = ['test' => 1, 'stage' => 4];
        $this->mock_update_marker->expects($this->exactly(1))->method('calculate_location_argument')
        ->with($expected_tile_specification)->willReturn('location_argument');
        $this->arrange();

        // Act
        $this->act_default();
        // Assert
    }

    protected function arrange() {
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
