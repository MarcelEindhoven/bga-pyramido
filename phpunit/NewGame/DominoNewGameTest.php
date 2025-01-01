<?php
namespace Bga\Games\PyramidoCannonFodder\NewGame;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/NewGame/DominoNewGame.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

class DominoNewGameTest extends TestCase{
    protected ?DominoNewGame $sut = null;
    protected ?Infrastructure\DominoFactory $mock_domino_factory = null;

    public function setup(): void {
        $this->mock_domino_factory = $this->createMock(Infrastructure\DominoFactory::class);
        $this->sut = new DominoNewGame();
        $this->sut->set_domino_factory($this->mock_domino_factory);

    }

    /**
     */
    public function test_domino_factory_add() {
        // Arrange
        $this->mock_domino_factory->expects($this->exactly(count(DominoNewGame::DOMINO_SPECIFICATION)))->method('add');
        // Act
        $this->sut->setup();
        // Assert
    }

    public function test_domino_factory_flush() {
        // Arrange
        $this->mock_domino_factory->expects($this->exactly(1))->method('flush');
        // Act
        $this->sut->setup();
        // Assert
    }
}
?>
