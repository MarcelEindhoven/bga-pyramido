<?php
namespace Bga\Games\Pyramido\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/ZombieMarkerChosenAndPlaced.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Domain/Pyramid.php');
use Bga\Games\Pyramido\Domain;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');
use Bga\Games\Pyramido\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class ZombieMarkerChosenAndPlacedTest extends TestCase{
    protected ?ZombieMarkerChosenAndPlaced $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateMarker $mock_update_marker = null;

    protected int $player_id = 77;
    protected string $quarry_index = 'quarry-2';

    protected array $current_data = ['tiles' => [77 => [1054 => ['tile']]],
            'candidate_tiles_for_marker' => [],
            'players' => [77 => ['name'=> 'x']],
    ];
    protected array $updated_marker = ['colour' => 1];
    protected array $expected_marker_specification = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = ZombieMarkerChosenAndPlaced::create($this->mock_gamestate);

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
        $tile_specification = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10]; // index 1054
        $tile = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 3, 'jewels' => []];
        $tile_index = Infrastructure\CurrentTiles::calculate_array_index($tile_specification);
        $this->current_data['tiles'][$this->player_id][$tile_index] = $tile;
        $this->current_data['candidate_tiles_for_marker'] = [$tile_specification];

        $this->mock_get_current_data->expects($this->exactly(3))->method('get')->willReturn(
            $this->current_data);
        $this->mock_update_marker->expects($this->once())->method('get_marker')
            ->with($this->player_id, $tile)->willReturn($this->updated_marker);

            $expected_tile_specification = $tile_specification;
        $this->arrange();

        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_double_candidate_tiles() {
        // Arrange
        $tile = ['stage' => 4, 'horizontal' => 10, 'vertical' => 10];
        $this->current_data['candidate_tiles_for_marker'] = [$tile, $tile];

        $this->mock_get_current_data->expects($this->exactly(3))->method('get')->willReturn($this->current_data);
        $this->mock_update_marker->expects($this->once())->method('get_marker')
            ->with($this->player_id, $this->current_data['tiles'][77][1054])->willReturn($this->updated_marker);

        $expected_tile_specification = $tile;
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
