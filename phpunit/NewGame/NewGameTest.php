<?php
namespace Bga\Games\PyramidoCannonFodder\NewGame;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/NewGame/NewGame.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class NewGameTest extends TestCase{
    protected ?NewGame $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    //protected ?Infrastructure\DominoFactory $mock_domino_factory = null;

    public function setup(): void {
        //$this->mock_domino_factory = $this->createMock(Infrastructure\DominoFactory::class);
        $this->sut = new NewGame();
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = NewGame::create(['domino' => $this->mock_cards]);

    }

    /**
     */
    public function test_integration_domino_creation() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('createCards');
        $this->mock_cards->expects($this->exactly(1))->method('shuffle')->with('deck');
        // Act
        $this->sut->setup_domino();
        // Assert
    }
}
?>