<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/AfterTurnFinished.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class AfterTurnFinishedTest extends TestCase{
    protected ?AfterTurnFinished $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;

    protected array $current_data = ['current_stage' => 2, 'candidate_positions' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]]];
    protected array $domino_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = AfterTurnFinished::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);
    }

    public function test_single_player_stage_1_partially_filled() {
        // Arrange
        $tiles = $this->create_tiles([1]);
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['tiles'=> [77 =>$tiles]]);

        // Act
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals('stage_not_finished', $transition_name);
    }

    public function test_single_player_stage_1_filled() {
        // Arrange
        $tiles = $this->create_tiles([20]);
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['tiles'=> [77 =>$tiles]]);

        // Act
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals('stage_finished', $transition_name);
    }

    public function test_single_player_stage_4_filled() {
        // Arrange
        $tiles = $this->create_tiles([20, 12, 6, 2]);
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['tiles'=> [77 =>$tiles]]);

        // Act
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals('stage_finished', $transition_name);
    }

    protected function create_tiles($number_tiles_per_stage): array {
        $tiles = [];
        $stage_number = 0;
        foreach ($number_tiles_per_stage as $number_tiles) {
            $stage_number = $stage_number + 1;
            for ($i=0; $i < $number_tiles; $i++)
                $tiles[] = ['stage' => $stage_number];
        }
        return $tiles;
    }
}

?>
