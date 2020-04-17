<?php
/**
 * AdvanceSale.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * AdvanceSale entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\AdvanceSaleRepository")
 * @ORM\Table(name="advance_sale", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdvanceSale extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    /**
     * id
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * theater
     *
     * @var Theater
     * @ORM\ManyToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $theater;

    /**
     * title
     *
     * @var Title
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $title;

    /**
     * publishing_expected_date
     *
     * @var \DateTime|null
     * @ORM\Column(type="date", name="publishing_expected_date", nullable=true)
     */
    protected $publishingExpectedDate;

    /**
     * publishing_expected_date_text
     *
     * @var string
     * @ORM\Column(type="string", name="publishing_expected_date_text", nullable=true)
     */
    protected $publishingExpectedDateText;

    /**
     * advance_tickets
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="AdvanceTicket", mappedBy="advanceSale", indexBy="id")
     */
    protected $advanceTickets;


    /**
     * construct
     */
    public function __construct()
    {
        $this->advanceTickets = new ArrayCollection();
    }

    /**
     * get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get tehater
     *
     * @return Theater
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * set theater
     *
     * @param Theater $theater
     * @return void
     */
    public function setTheater(Theater $theater)
    {
        $this->theater = $theater;
    }

    /**
     * get title
     *
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @param Title $title
     * @return void
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;
    }

    /**
     * get publishing_expected_date
     *
     * @return \DateTime|null
     */
    public function getPublishingExpectedDate()
    {
        return $this->publishingExpectedDate;
    }

    /**
     * set publishing_dxpected_date
     *
     * @param \DateTime|string|null $publishingExpectedDate
     * @return void
     */
    public function setPublishingExpectedDate($publishingExpectedDate)
    {
        if (is_null($publishingExpectedDate) || ($publishingExpectedDate instanceof \DateTime)) {
            $this->publishingExpectedDate = $publishingExpectedDate;
        } else {
            $this->publishingExpectedDate = new \DateTime($publishingExpectedDate);
        }
    }

    /**
     * get publishing_expected_date_text
     *
     * @return string
     */
    public function getPublishingExpectedDateText()
    {
        return $this->publishingExpectedDateText;
    }

    /**
     * set publishing_expected_date_text
     *
     * @param string $publishingExpectedDateText
     * @return void
     */
    public function setPublishingExpectedDateText(string $publishingExpectedDateText)
    {
        $this->publishingExpectedDateText = $publishingExpectedDateText;
    }

    /**
     * get advance_tickets
     *
     * @return Collection
     */
    public function getAdvanceTickets()
    {
        return $this->advanceTickets;
    }

    /**
     * get active advance_tickets
     *
     * @return Collection
     */
    public function getActiveAdvanceTickets()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isDeleted', false));

        /**
         * matching()を使うとindexByオプションの設定が消えてしまう
         * https://github.com/doctrine/doctrine2/issues/4693
         */
        $tmpResults = $this->getAdvanceTickets()->matching($criteria);

        // idをindexにしたcollectionを作り直す
        $results = new ArrayCollection();

        foreach ($tmpResults as $tmp) {
            /** @var AdvanceTicket $tmp */
            $results->set($tmp->getId(), $tmp);
        }

        return $results;
    }
}
