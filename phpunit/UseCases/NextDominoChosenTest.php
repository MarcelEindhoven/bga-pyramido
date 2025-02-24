<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/NextDominoChosen.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class NextDominoChosenTest extends TestCase{
    protected ?NextDominoChosen $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateMarket $mock_update_market = null;

    protected string $quarry_index = 'quarry-1';
    protected string $next_index = 'next-2';

    protected array $current_data_first = ['market' => [2 => ['id' => 9, 'tiles'=> ['a', 'b']]]];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = NextDominoChosen::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_market = $this->createMock(Infrastructure\UpdateMarket::class);
        $this->sut->set_update_market($this->mock_update_market);

        $this->sut->set_quarry_index($this->quarry_index);

        $this->sut->set_next_index($this->next_index);
    }

    public function test_execute_moves_domino() {
        // Arrange
        $this->arrange_default();
        $this->mock_update_market->expects($this->exactly(1))->method('move')->with($this->next_index, $this->quarry_index);
        // Act
        $this->act_default();
        // Assert
    }

    public function test_execute_refills_next() {
        // Arrange
        $this->arrange_default();
        $this->mock_update_market->expects($this->exactly(1))->method('refill')->with($this->next_index);
        // Act
        $this->act_default();
        // Assert
    }

    protected function arrange_default() {
        $this->mock_update_market->expects($this->exactly(1))->method('get_market_entries')->with('next')->willReturn([1 => ['id' => 1], 2 => ['id' => 2], 3 => ['id' => 3], 4 => ['id' => 4], ]);
    }

    protected function act_default() {
        $this->sut->execute();
    }
}

?>
