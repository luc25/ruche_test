<?php

namespace App\Service;

use App\Entity\Gift;
use App\Entity\Receiver;
use App\Entity\Warehouse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GiftService
{
    private const VALID_DATA_STRUCTURE = ['gift_uuid', 'gift_code', 'gift_description', 'gift_price', 'receiver_uuid', 'receiver_first_name', 'receiver_last_name', 'receiver_country_code'];

    private $em;

    private $receivers = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createGiftsFromFile(string $warehouse, UploadedFile $file): int
    {
        if ($file->getClientOriginalExtension() != 'csv') {
            return 1;
        }

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $gifts = $serializer->decode($file->getContent(), 'csv', ['csv_delimiter' => ',']);
        $warehouse = $this->getWarehouse($warehouse);

        for ($i = 0; $i < count($gifts); ++$i) {
            if (array_keys($gifts[$i]) != self::VALID_DATA_STRUCTURE) {
                continue;
            }

            $gift = new Gift();
            $gift->setUid($gifts[$i]['gift_uuid']);
            $gift->setCode($gifts[$i]['gift_code']);
            $gift->setDescription($gifts[$i]['gift_description']);
            $gift->setPrice($gifts[$i]['gift_price']);
            $gift->setWarehouse($warehouse);
            $gift->setReceiver($this->getReceiver($gifts[$i]));

            $this->em->persist($gift);

            if (($i % 20) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();

        return 0;
    }

    private function getWarehouse(string $name): Warehouse
    {
        $warehouse = $this->em->getRepository(Warehouse::class)->findOneBy(['name' => strtolower($name)]);

        if (!$warehouse instanceof Warehouse) {
            $warehouse = new Warehouse();
            $warehouse->setName(strtolower($name));

            $this->em->persist($warehouse);
        }

        return $warehouse;
    }

    private function getReceiver(array $data): Receiver
    {
        if (isset($this->receivers[$data['receiver_uuid']])) {
            return $this->receivers[$data['receiver_uuid']];
        }

        $receiver = $this->em->getRepository(Receiver::class)->findOneBy(['uid' => $data['receiver_uuid']]);

        if (!$receiver instanceof Receiver) {
            $receiver = new Receiver();
            $receiver->setUid($data['receiver_uuid']);
            $receiver->setFirstname($data['receiver_first_name']);
            $receiver->setLastname($data['receiver_last_name']);
            $receiver->setCountryCode($data['receiver_country_code']);

            $this->em->persist($receiver);
        }

        $this->receivers[$data['receiver_uuid']] = $receiver;

        return $receiver;
    }

    public function getStatistics(?string $warehouseName): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('totalGifts', 'totalGifts');
        $rsm->addScalarResult('totalCountries', 'totalCountries');
        $rsm->addScalarResult('averagePrice', 'averagePrice');
        $rsm->addScalarResult('minPrice', 'minPrice');
        $rsm->addScalarResult('maxPrice', 'maxPrice');
        $query = <<<SQL
select COUNT(*) as totalGifts, COUNT(DISTINCT r.country_code) as totalCountries, ROUND(AVG(g.price), 2) as averagePrice,
       MIN(g.price) as minPrice, MAX(g.price) as maxPrice
from gift g
inner join receiver r on g.receiver_id = r.id
SQL;

        if ($warehouseName) {
            $query .= <<<SQL
 inner join warehouse w on g.warehouse_id = w.id
WHERE w.name = ?
SQL;
        }

        $query = $this->em->createNativeQuery($query, $rsm);
        $query->setParameter(1, $warehouseName);

        return $query->getResult()[0];
    }
}
