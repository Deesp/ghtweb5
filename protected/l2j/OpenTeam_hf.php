<?php

class OpenTeam_hf
{
    /**
     * Объект подключения к БД
     * @var CDbConnection
     */
    private $_db;

    /**
     * Объект класса Lineage
     * @var Lineage
     */
    private $_context;

    /**
     * Поля из БД
     * @var array
     */
    private $_fields = array(
        'accounts.access_level'     => 'accounts.access_level',
        'characters.access_level'   => 'characters.accesslevel',
        'characters.char_id'        => 'characters.obj_Id',
        'clan_data.clan_id'         => 'clan_data.clan_id',
    );


    public function __construct($context)
    {
        $this->_context = $context;
        $this->_db = $context->getDb();
    }

    /**
     * Создание игрового аккаунта
     *
     * @param $login
     * @param $password
     * @param int $access_level
     *
     * @return bool
     */
    public function insertAccount($login, $password, $access_level = 0)
    {
        return $this->_db->createCommand()->insert('accounts', array(
            'login'        => $login,
            'password'     => $this->_context->passwordEncrypt($password),
            'access_level' => $access_level,
        ));
    }

    /**
     * Возвращает аккаунты
     *
     * @param CDbCommand $command
     *
     * @return CDbCommand
     */
    public function accounts($command = NULL)
    {
        if(!($command instanceof CDbCommand))
        {
            $command = $this->_db->createCommand();
        }

        return $command->select('login, password, lastactive AS last_active, access_level')
            ->from('accounts');
    }

    /**
     * Возвращает список персонажей
     *
     * @param CDbCommand $command
     *
     * @return CDbCommand
     */
    public function characters($command = NULL)
    {
        if(!($command instanceof CDbCommand))
        {
            $command = $this->_db->createCommand();
        }

        /*
            SELECT
            characters.account_name,characters.obj_Id AS char_id,characters.char_name,characters.sex,characters.x,characters.y,characters.z,characters.karma,characters.pvpkills,characters.pkkills,characters.clanid AS clan_id,characters.title,
            characters.online,characters.onlinetime,character_subclasses.class_id AS base_class,character_subclasses.level,character_subclasses.exp,character_subclasses.sp,character_subclasses.maxHp,character_subclasses.maxMp,character_subclasses.maxCp,
            character_subclasses.curHp,character_subclasses.curMp,character_subclasses.curCp,clan_data.clan_level,clan_data.hasCastle,clan_data.hasFortress AS hasFort,clan_data.crest AS clan_crest,clan_data.reputation_score,clan_data.clan_name,
            characters.accesslevel AS access_level,(SELECT IF(value>0,1,0) FROM character_variables WHERE character_variables.obj_id = characters.obj_Id AND character_variables.name = "jailed" LIMIT 1) as jail
            FROM
            characters
            LEFT JOIN character_subclasses ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1
            LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
        */
        return $command
            ->select('characters.account_name,characters.obj_Id AS char_id,characters.char_name,characters.sex,characters.x,characters.y,characters.z,characters.karma,characters.pvpkills,characters.pkkills,characters.clanid AS clan_id,characters.title,
                characters.online,characters.onlinetime,character_subclasses.class_id AS base_class,character_subclasses.level,character_subclasses.exp,character_subclasses.sp,character_subclasses.maxHp,character_subclasses.maxMp,character_subclasses.maxCp,
                character_subclasses.curHp,character_subclasses.curMp,character_subclasses.curCp,clan_data.clan_level,clan_data.hasCastle,clan_data.hasFortress AS hasFort,clan_data.crest AS clan_crest,clan_data.reputation_score,clan_data.clan_name,
                characters.accesslevel AS access_level,(SELECT IF(value>0,1,0) FROM character_variables WHERE character_variables.obj_id = characters.obj_Id AND character_variables.name = "jailed" LIMIT 1) as jail')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->from('characters');
    }

    /**
     * Кланы + инфа о лидере + кол-во персонажей в клане
     *
     * @param CDbCommand $command
     *
     * @return CDbCommand
     */
    public function clans($command = NULL)
    {
        if(!($command instanceof CDbCommand))
        {
            $command = $this->_db->createCommand();
        }

        /*
            SELECT
            clan_data.clan_id,clan_data.clan_name,clan_data.clan_level,clan_data.hasCastle,clan_data.hasFortress,clan_data.ally_id,ally_data.ally_name,clan_data.leader_id,clan_data.crest AS clan_crest,clan_data.largecrest AS clan_crest_large,ally_data.crest AS ally_crest,
            clan_data.reputation_score,characters.account_name,characters.obj_Id AS char_id,characters.char_name,character_subclasses.level,character_subclasses.maxHp,character_subclasses.curHp,character_subclasses.maxCp,character_subclasses.curCp,character_subclasses.maxMp,
            character_subclasses.curMp,characters.sex,characters.x,characters.y,characters.z,character_subclasses.exp,character_subclasses.sp,characters.karma,characters.pvpkills,characters.pkkills,character_subclasses.class_id AS base_class,characters.title,characters.`online`,characters.onlinetime,
            (SELECT COUNT(0) FROM characters WHERE characters.clanid = clan_data.clan_id) as ccount
            FROM
            clan_data
            LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id
            LEFT JOIN characters ON clan_data.leader_id = characters.obj_Id
            LEFT JOIN character_subclasses ON characters.obj_Id = character_subclasses.char_obj_id
            WHERE
            character_subclasses.isBase = 1
        */
        return $command
            ->select('clan_data.clan_id,clan_data.clan_name,clan_data.clan_level,clan_data.hasCastle,clan_data.hasFortress,clan_data.ally_id,ally_data.ally_name,clan_data.leader_id,clan_data.crest AS clan_crest,clan_data.largecrest AS clan_crest_large,ally_data.crest AS ally_crest,
                clan_data.reputation_score,characters.account_name,characters.obj_Id AS char_id,characters.char_name,character_subclasses.level,character_subclasses.maxHp,character_subclasses.curHp,character_subclasses.maxCp,character_subclasses.curCp,character_subclasses.maxMp,
                character_subclasses.curMp,characters.sex,characters.x,characters.y,characters.z,character_subclasses.exp,character_subclasses.sp,characters.karma,characters.pvpkills,characters.pkkills,character_subclasses.class_id AS base_class,characters.title,characters.`online`,characters.onlinetime,
                (SELECT COUNT(0) FROM characters WHERE characters.clanid = clan_data.clan_id) as ccount')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->leftJoin('characters', 'clan_data.leader_id = characters.obj_Id')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id')
            ->andWhere('character_subclasses.isBase = 1')
            ->from('clan_data');
    }

    /**
     * Возвращает предметы
     *
     * @param CDbCommand $command
     *
     * @return CDbCommand
     */
    public function items($command = NULL)
    {
        if(!($command instanceof CDbCommand))
        {
            $command = $this->_db->createCommand();
        }

        return $command
            ->select('owner_id,object_id,item_id,count,enchant_level,loc,loc_data')
            ->from('items');
    }

    /**
     * Добавление предмета в игру
     *
     * @param int $ownerId
     * @param int $itemId
     * @param int $count
     * @param int $enchantLevel
     *
     * @return bool
     */
    public function insertItem($ownerId, $itemId, $count = 1, $enchantLevel = 0)
    {
        return $this->_db->createCommand()
            ->insert('items_delayed', array(
                'owner_id'      => $ownerId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'description'   => 'GHTWEB',
            ));
    }

    /**
     * Добавление дохуя предметов в игру
     *
     * @param array $items
     *
     * <code>
     *     array(
     *         'owner_id' => 111111
     *         'item_id' => 57
     *         'count' => 100
     *         'enchant' => 0
     *     )
     * </code>
     *
     * @return bool
     */
    public function multiInsertItem(array $items)
    {
        // Докидываю необходимые данные
        foreach($items as $k => $v)
        {
            $items[$k]['description'] = 'GHTWEB';
            $items[$k]['enchant_level'] = $v['enchant'];

            unset($items[$k]['enchant']);
        }

        $builder = $this->_db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand('items_delayed', $items);
        return $command->execute();
    }

    private function _races()
    {
        $data   = app()->params['l2']['classList'];
        $return = array();

        foreach($data as $k => $v)
        {
            $return[$v['race']][] = $k;
        }

        return $return;
    }

    private function getCountRacesById($id)
    {
        $races = $this->_races();

        return $this->_db->createCommand()
            ->select('COUNT(0)')
            ->where(array('in', 'class_id', $races[$id]))
            ->andWhere('isBase = 1')
            ->from('{{character_subclasses}}')
            ->queryScalar();
    }

    /**
     * Возвращает кол-во людей
     *
     * @return int
     */
    public function getCountRaceHuman()
    {
        return $this->getCountRacesById(0);
    }

    /**
     * Возвращает кол-во эльфов
     *
     * @return int
     */
    public function getCountRaceElf()
    {
        return $this->getCountRacesById(1);
    }

    /**
     * Возвращает кол-во тэмных эльфов
     *
     * @return int
     */
    public function getCountRaceDarkElf()
    {
        return $this->getCountRacesById(2);
    }

    /**
     * Возвращает кол-во орков
     *
     * @return int
     */
    public function getCountRaceOrk()
    {
        return $this->getCountRacesById(3);
    }

    /**
     * Возвращает кол-во гномов
     *
     * @return int
     */
    public function getCountRaceDwarf()
    {
        return $this->getCountRacesById(4);
    }

    /**
     * Возвращает кол-во камаэлей
     *
     * @return int
     */
    public function getCountRaceKamael()
    {
        return $this->getCountRacesById(5);
    }


    /**
     * Возвращает кол-во аккаунтов
     *
     * @return int
     */
    public function getCountAccounts()
    {
        return $this->_db->createCommand("SELECT COUNT(0) FROM {{accounts}}")->queryScalar();
    }

    /**
     * Возвращает кол-во персонажей
     *
     * @return int
     */
    public function getCountCharacters()
    {
        return $this->_db->createCommand("SELECT COUNT(0) FROM {{characters}}")->queryScalar();
    }

    /**
     * Возвращает кол-во персонажей в игре
     *
     * @return int
     */
    public function getCountOnlineCharacters()
    {
        return $this->_db->createCommand("SELECT COUNT(0) FROM {{characters}} WHERE online = 1")->queryScalar();
    }

    /**
     * Возвращает кол-во кланов
     *
     * @return int
     */
    public function getCountClans()
    {
        return $this->_db->createCommand("SELECT COUNT(0) FROM {{clan_data}}")->queryScalar();
    }

    /**
     * Возвращает кол-во мужчин
     *
     * @return int
     */
    public function getCountMen()
    {
        return $this->_db->createCommand("SELECT COUNT(0) FROM {{characters}} WHERE sex = 0")->queryScalar();
    }

    /**
     * Возвращает кол-во женщин
     *
     * @return int
     */
    public function getCountWomen()
    {
        return $this->_db->createCommand("SELECT COUNT(0) FROM {{characters}} WHERE sex = 1")->queryScalar();
    }

    /**
     * Возвращает Топ ПВП
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getTopPvp($limit = 20, $offset = 0)
    {
        $command = $this->_db->createCommand()
            ->where('pvpkills > 0')
            ->andWhere($this->_fields['characters.access_level'] . ' = 0')
            ->order('pvpkills DESC')
            ->limit($limit)
            ->offset($offset);

        return $this->characters($command)->queryAll();
    }

    /**
     * Возвращает Топ ПК
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getTopPk($limit = 20, $offset = 0)
    {
        $command = $this->_db->createCommand()
            ->where('pkkills > 0')
            ->andWhere($this->_fields['characters.access_level'] . ' = 0')
            ->order('pkkills DESC')
            ->limit($limit)
            ->offset($offset);

        return $this->characters($command)->queryAll();
    }

    /**
     * Возвращает Топ игроков
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getTop($limit = 20, $offset = 0)
    {
        $command = $this->_db->createCommand()
            ->where($this->_fields['characters.access_level'] . ' = 0')
            ->order('exp DESC')
            ->limit($limit)
            ->offset($offset);

        return $this->characters($command)->queryAll();
    }

    /**
     * Возвращает Топ богачей
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getTopRich($limit = 20, $offset = 0)
    {
        $command = $this->characters();

        $command->select = $command->select . ',SUM(items.count) AS adena_count';
        $command->where  = 'items.item_id = 57';
        $command->andWhere($this->_fields['characters.access_level'] . ' = 0');
        $command->order  = 'adena_count DESC';
        $command->limit  = $limit;
        $command->offset = $offset;
        $command->group  = $this->getField('characters.char_id');
        $command->leftJoin('items', 'items.owner_id = ' . $this->getField('characters.char_id'));

        return $command->queryAll();
    }

    /**
     * Возвращает кто в игре
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getOnline($limit = 20, $offset = 0)
    {
        $command = $this->_db->createCommand();

        $command->where  = 'online = 1';
        $command->andWhere($this->_fields['characters.access_level'] . ' = 0');
        $command->order  = 'level DESC';
        $command->limit  = $limit;
        $command->offset = $offset;

        return $this->characters($command)->queryAll();
    }

    /**
     * Возвращает Топ кланов
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getTopClans($limit = 20, $offset = 0)
    {
        $command = $this->_db->createCommand();

        $command->andWhere($this->_fields['characters.access_level'] . ' = 0');
        $command->order  = 'reputation_score DESC';
        $command->limit  = $limit;
        $command->offset = $offset;

        return $this->clans($command)->queryAll();
    }

    /**
     * Возвращает список замков и инфу о владельцах
     *
     * @return array
     */
    public function getCastles()
    {
        /*
            SELECT
            castle.id,castle.name,castle.taxPercent AS taxPercent,castle.siegeDate AS siegeDate,clan_data.clan_id,clan_data.clan_level,clan_data.reputation_score,clan_data.hasCastle,clan_data.hasFortress AS hasFort,clan_subpledges.name AS clan_name,clan_data.ally_id,
            ally_data.ally_name,clan_subpledges.leader_id,clan_data.crest AS clan_crest,clan_data.largecrest AS clan_crest_large,ally_data.crest AS ally_crest
            FROM
            castle
            LEFT JOIN clan_data ON clan_data.hasCastle = castle.id
            LEFT JOIN clan_subpledges ON clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0
            LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id
         */
        return $this->_db->createCommand()
            ->select('castle.id,castle.name,castle.taxPercent AS taxPercent,castle.siegeDate AS siegeDate,clan_data.clan_id,clan_data.clan_level,clan_data.reputation_score,clan_data.hasCastle,clan_data.hasFortress AS hasFort,clan_subpledges.name AS clan_name,clan_data.ally_id,
                ally_data.ally_name,clan_subpledges.leader_id,clan_data.crest AS clan_crest,clan_data.largecrest AS clan_crest_large,ally_data.crest AS ally_crest')
            ->leftJoin('clan_data', 'clan_data.hasCastle = castle.id')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->from('castle')
            ->queryAll();
    }

    public function getSiege()
    {
        return $this->_db->createCommand()
            ->select('siege_clans.unit_id AS residence_id AS castle_id,siege_clans.clan_id,siege_clans.type,clan_subpledges.name AS clan_name,clan_data.clan_level,clan_data.reputation_score,clan_data.hasCastle,clan_data.hasFortress AS hasFort,
                clan_data.ally_id,ally_data.ally_name,clan_subpledges.leader_id,clan_data.crest AS clan_crest,clan_data.largecrest AS clan_crest_large,ally_data.crest AS ally_crest')
            ->leftJoin('clan_data', 'siege_clans.clan_id = clan_data.clan_id')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->where('clan_subpledges.type = 0')
            ->from('siege_clans')
            ->queryAll();
    }

    /**
     * Хроники сервера
     *
     * @return string
     */
    public function getChronicle()
    {
        return 'hf';
    }

    /**
     * Возвращает название поля из таблицы
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getField($fieldName)
    {
        return isset($this->_fields[$fieldName]) ? $this->_fields[$fieldName] : NULL;
    }

    /**
     * Возвращает название сервера
     *
     * @return string
     */
    public function getServerName()
    {
        return __CLASS__;
    }

    /**
     * Информация о премиум аккаунте
     * Возвращает дату окончания в timestamp формате
     *
     * @param string $accountName
     *
     * @return array
     *
     * <code>
     *     array(
     *         'dateEnd' => 1234567890
     *     )
     * </code>
     *
     */
    public function getPremiumInfo($accountName)
    {
        $res = $this->_db->createCommand("SELECT * FROM {{accounts}} WHERE login = :account_name AND bonus_expire > 0 LIMIT 1")
            ->bindParam('account_name', $accountName, PDO::PARAM_STR)
            ->queryRow();

        return array(
            'dateEnd' => ($res && $res['bonus_expire'] > 0 ? substr($res['bonus_expire'], 0, 10) : 0),
        );
    }

    /**
     * Добавление времени к премиум аккаунту
     *
     * @param string $accountName
     * @param int $timeEnd
     *
     * @return bool
     */
    public function addPremium($accountName, $timeEnd)
    {
        return $this->_db->createCommand()->update('{{accounts}}', array('bonus_expire' => $timeEnd), 'login = :login', array('login' => $accountName));
    }

    /**
     * Удаление привязки по HWID
     *
     * @param string $accountName
     *
     * @return bool
     */
    public function removeHWID($accountName)
    {
        // @TODO, пока непонятно где в БД идёт привязка
        /*return $this->_db->createCommand("UPDATE {{accounts}} SET allowed_hwid = '*' WHERE login = :login LIMIT 1")
            ->bindParam(':login', $accountName, PDO::PARAM_STR)
            ->execute();*/
        return TRUE;
    }

    /**
     * Контроль предметов
     *
     * @param array $itemsIds
     *
     * @return array
     */
    public function getItemsControl(array $itemsIds)
    {
        if(!$itemsIds)
        {
            return array();
        }

        $res = AllItems::model()->findAllByAttributes(array(
            'item_id' => $itemsIds,
        ));

        $itemNames = array();

        foreach($res as $row)
        {
            $itemNames[$row->getPrimaryKey()] = $row;
        }

        unset($res);

        /*
            SELECT
            Max(items.count) AS maxCountItems,Count(items.count) AS countItems,items.owner_id,items.object_id,items.item_id,items.count,items.enchant_level,items.loc,items.loc_data,characters.obj_Id AS char_id,characters.account_name,characters.char_name,
            characters.x,characters.y,characters.z,characters.pvpkills,characters.pkkills,characters.clanid AS clan_id,characters.title,characters.`online`,characters.onlinetime,clan_data.clan_name,clan_data.clan_level,clan_data.hasFortress AS hasFort,
            clan_data.hasCastle,clan_data.crest AS clan_crest,clan_data.reputation_score,character_subclasses.class_id as base_class,character_subclasses.`level`,character_subclasses.exp,character_subclasses.sp,character_subclasses.curHp,
            character_subclasses.curMp,character_subclasses.curCp,character_subclasses.maxHp,character_subclasses.maxMp,character_subclasses.maxCp
            FROM
            items
            LEFT JOIN characters ON items.owner_id = characters.obj_Id
            LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
            LEFT JOIN character_subclasses ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1
            WHERE
            items.item_id IN (57, 4037, 5588, 10)
            GROUP BY
            items.owner_id,
            items.item_id
            ORDER BY
            items.item_id ASC
         */
        $res = $this->_db->createCommand()
            ->select("Max(items.count) AS maxCountItems,Count(items.count) AS countItems,items.owner_id,items.object_id,items.item_id,items.count,items.enchant_level,items.loc,items.loc_data,characters.obj_Id AS char_id,characters.account_name,characters.char_name,
                characters.x,characters.y,characters.z,characters.pvpkills,characters.pkkills,characters.clanid AS clan_id,characters.title,characters.`online`,characters.onlinetime,clan_data.clan_name,clan_data.clan_level,clan_data.hasFortress AS hasFort,
                clan_data.hasCastle,clan_data.crest AS clan_crest,clan_data.reputation_score,character_subclasses.class_id as base_class,character_subclasses.`level`,character_subclasses.exp,character_subclasses.sp,character_subclasses.curHp,
                character_subclasses.curMp,character_subclasses.curCp,character_subclasses.maxHp,character_subclasses.maxMp,character_subclasses.maxCp")
            ->leftJoin('characters', 'items.owner_id = characters.obj_Id')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1')
            ->where(array('in', 'item_id', $itemsIds))
            ->group('items.owner_id, items.item_id')
            ->order('maxCountItems DESC')
            ->from('items')
            ->queryAll();

        $characters = array();

        foreach($res as $item)
        {
            if(!isset($characters[$item['item_id']]['maxTotalItems']))
            {
                $characters[$item['item_id']]['maxTotalItems'] = 0;
            }

            $characters[$item['item_id']]['itemInfo'] = $itemNames[$item['item_id']];
            $characters[$item['item_id']]['characters'][] = $item;
            $characters[$item['item_id']]['maxTotalItems'] += $item['maxCountItems'];
            $characters[$item['item_id']]['totalItems'] = count($characters[$item['item_id']]['characters']);
        }

        foreach(array_diff_key($itemNames, $characters) as $item)
        {
            $characters[$item->item_id]['itemInfo'] = $item;
            $characters[$item->item_id]['characters'] = array();
            $characters[$item->item_id]['maxTotalItems'] = 0;
            $characters[$item->item_id]['totalItems'] = 0;
        }

        return $characters;
    }
}
