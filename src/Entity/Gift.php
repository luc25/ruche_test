<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Gift
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Receiver", cascade={"persist"})
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", nullable=false)
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="Warehouse", cascade={"persist"})
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id", nullable=false)
     */
    private $warehouse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getReceiver(): ?Receiver
    {
        return $this->receiver;
    }

    public function setReceiver(Receiver $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }
}
