<?php

/*
 * Copyright 2015-2018 Mathieu Piot.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Chromosome entity.
 *
 * @ORM\Table(name="chromosome")
 * @ORM\Entity(repositoryClass="App\Repository\ChromosomeRepository")
 */
class Chromosome
{
    /**
     * The ID in the database.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The name of the chromosome.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * An array of accessions.
     *
     * @var array
     *
     * @ORM\Column(name="accessions", type="array", nullable=true)
     */
    private $accessions;

    /**
     * The chromosome description.
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * An array of keywords for the chromosome.
     *
     * @var array
     *
     * @ORM\Column(name="keywords", type="array")
     */
    private $keywords;

    /**
     * The project ID.
     *
     * @var string
     *
     * @ORM\Column(name="projectId", type="string", length=255, nullable=true)
     */
    private $projectId;

    /**
     * When the chromosome was created.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * The num created.
     *
     * @var int
     *
     * @ORM\Column(name="numCreated", type="integer", nullable=true)
     */
    private $numCreated;

    /**
     * When the chromosome was released.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="dateReleased", type="datetime")
     */
    private $dateReleased;

    /**
     * The numReleased.
     *
     * @var int
     *
     * @ORM\Column(name="numReleased", type="integer", nullable=true)
     */
    private $numReleased;

    /**
     * The version of the chromosome.
     *
     * @var int
     *
     * @ORM\Column(name="numVersion", type="integer", nullable=true)
     */
    private $numVersion;

    /**
     * The length of the chromosome.
     *
     * @var int
     *
     * @ORM\Column(name="length", type="integer")
     */
    private $length;

    /**
     * The G/C percent.
     *
     * @var float
     *
     * @ORM\Column(name="gc", type="float")
     */
    private $gc;

    /**
     * The number of CDS.
     *
     * @var int
     *
     * @ORM\Column(name="cdsCount", type="integer")
     */
    private $cdsCount;

    /**
     * Is it mitochondrial ?
     * true -> yes, false -> no.
     *
     * @var bool
     *
     * @ORM\Column(name="mitochondrial", type="boolean")
     */
    private $mitochondrial;

    /**
     * A comment on this chromosome.
     *
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * The strain that owned the chromosome.
     *
     * @var Strain
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Strain", inversedBy="chromosomes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $strain;

    /**
     * The DNA sequence of the chromosome.
     *
     * @var DnaSequence
     *
     * @ORM\OneToOne(targetEntity="App\Entity\DnaSequence", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $dnaSequence;

    /**
     * Flat files of the chromsomes.
     *
     * @var FlatFile|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FlatFile", mappedBy="chromosome", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $flatFiles;

    /**
     * A slug for url.
     *
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     */
    private $slug;

    /**
     * The source.
     *
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255)
     */
    private $source;

    /**
     * Locus.
     *
     * @var Locus|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Locus", mappedBy="chromosome", cascade={"persist", "remove"})
     */
    private $locus;

    /**
     * Chromosome constructor.
     */
    public function __construct()
    {
        $this->accessions = [];
        $this->keywords = [];
        $this->flatFiles = new ArrayCollection();
        $this->seos = new ArrayCollection();
        $this->locus = new ArrayCollection();
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Add accession.
     *
     * @param string $accession
     */
    public function addAccession($accession): self
    {
        if (!empty($accession) && !\in_array($accession, $this->accessions, true)) {
            $this->accessions[] = $accession;
        }

        return $this;
    }

    /**
     * Remove accession.
     *
     * @param string $accession
     */
    public function removeAccession($accession): self
    {
        if (false !== $key = array_search($accession, $this->accessions, true)) {
            unset($this->accessions[$key]);
            $this->accessions = array_values($this->accessions);
        }

        return $this;
    }

    /**
     * Empty accessions.
     */
    public function emptyAccessions(): self
    {
        $this->accessions = [];

        return $this;
    }

    /**
     * Set accession.
     *
     * @param array $accessions
     */
    public function setAccession($accessions): self
    {
        if (null !== $accessions) {
            foreach ($accessions as $accession) {
                $this->addAccession($accession);
            }
        }

        return $this;
    }

    /**
     * Get accession.
     */
    public function getAccessions(): array
    {
        return $this->accessions;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Add keyword.
     *
     * @param string $keyword
     */
    public function addKeyword($keyword): self
    {
        if (!empty($keyword) && !\in_array($keyword, $this->keywords, true)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    /**
     * Remove keyword.
     *
     * @param string $keyword
     */
    public function removeKeyword($keyword): self
    {
        if (false !== $key = array_search($keyword, $this->keywords, true)) {
            unset($this->keywords[$key]);
            $this->keywords = array_values($this->keywords);
        }

        return $this;
    }

    /**
     * Empty keywords.
     */
    public function emptyKeywords(): self
    {
        $this->keywords = [];

        return $this;
    }

    /**
     * Set keywords.
     *
     * @param array $keywords
     */
    public function setKeywords($keywords): self
    {
        if (null === $keywords) {
            return $this;
        }

        foreach ($keywords as $keyword) {
            $this->addKeyword($keyword);
        }

        return $this;
    }

    /**
     * Get keywords.
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * Set projectId.
     *
     * @param string $projectId
     */
    public function setProjectId($projectId = null): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId.
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * Set dateCreated.
     */
    public function setDateCreated(\DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * Set numCreated.
     *
     * @param int $numCreated
     */
    public function setNumCreated($numCreated): self
    {
        $this->numCreated = $numCreated;

        return $this;
    }

    /**
     * Get numCreated.
     */
    public function getNumCreated(): int
    {
        return $this->numCreated;
    }

    /**
     * Set dateReleased.
     */
    public function setDateReleased(\DateTime $dateReleased): self
    {
        $this->dateReleased = $dateReleased;

        return $this;
    }

    /**
     * Get dateReleased.
     */
    public function getDateReleased(): \DateTime
    {
        return $this->dateReleased;
    }

    /**
     * Set numReleased.
     *
     * @param int $numReleased
     */
    public function setNumReleased($numReleased): self
    {
        $this->numReleased = $numReleased;

        return $this;
    }

    /**
     * Get numReleased.
     */
    public function getNumReleased(): int
    {
        return $this->numReleased;
    }

    /**
     * Set numVersion.
     *
     * @param int $numVersion
     */
    public function setNumVersion($numVersion): self
    {
        $this->numVersion = $numVersion;

        return $this;
    }

    /**
     * Get numVersion.
     */
    public function getNumVersion(): int
    {
        return $this->numVersion;
    }

    /**
     * Set length.
     *
     * @param int $length
     */
    public function setLength($length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length.
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Set gc.
     *
     * @param float $gc
     */
    public function setGc($gc): self
    {
        $this->gc = $gc;

        return $this;
    }

    /**
     * Get gc.
     */
    public function getGc(): float
    {
        return $this->gc;
    }

    /**
     * Set cdsCount.
     *
     * @param int $cdsCount
     */
    public function setCdsCount($cdsCount): self
    {
        $this->cdsCount = $cdsCount;

        return $this;
    }

    /**
     * Get cdsCount.
     */
    public function getCdsCount(): int
    {
        return $this->cdsCount;
    }

    /**
     * Set mitochondrial.
     *
     * @param bool $mitochondrial
     */
    public function setMitochondrial($mitochondrial): self
    {
        $this->mitochondrial = $mitochondrial;

        return $this;
    }

    /**
     * Get mitochondrial.
     */
    public function getMitochondrial(): bool
    {
        return $this->mitochondrial;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     */
    public function setComment($comment = null): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Set strain.
     *
     *
     * @return $this
     */
    public function setStrain(Strain $strain)
    {
        $this->strain = $strain;

        return $this;
    }

    /**
     * Get strain.
     */
    public function getStrain(): Strain
    {
        return $this->strain;
    }

    /**
     * Set DnaSequence.
     *
     *
     * @return $this
     */
    public function setDnaSequence(DnaSequence $dnaSequence)
    {
        $this->dnaSequence = $dnaSequence;

        return $this;
    }

    /**
     * Get DnaSequence.
     */
    public function getDnaSequence(): DnaSequence
    {
        return $this->dnaSequence;
    }

    /**
     * Add FlatFile.
     *
     *
     * @return $this
     */
    public function addFlatFile(FlatFile $flatFile)
    {
        if (!$this->flatFiles->contains($flatFile)) {
            $this->flatFiles[] = $flatFile;
            $flatFile->setChromosome($this);
        }

        return $this;
    }

    /**
     * Remove FlatFile.
     *
     *
     * @return $this
     */
    public function removeFlatFile(FlatFile $flatFile)
    {
        if ($this->flatFiles->contains($flatFile)) {
            $this->flatFiles->removeElement($flatFile);
        }

        return $this;
    }

    /**
     * Get FlatFile.
     *
     * @return FlatFile|ArrayCollection
     */
    public function getFlatFiles()
    {
        return $this->flatFiles;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set source.
     */
    public function setSource($source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Add Locus.
     *
     *
     * @return $this
     */
    public function addLocus(Locus $locus)
    {
        if (!$this->locus->contains($locus)) {
            $this->locus[] = $locus;
            $locus->setChromosome($this);
        }

        return $this;
    }

    /**
     * Remove Locus.
     *
     *
     * @return $this
     */
    public function removeLocus(Locus $locus)
    {
        if ($this->locus->contains($locus)) {
            $this->locus->removeElement($locus);
        }

        return $this;
    }

    /**
     * Get Locus.
     *
     * @return Locus|ArrayCollection
     */
    public function getLocus()
    {
        return $this->locus;
    }
}
