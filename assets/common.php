<?php
function getPassedTimeString($time=0) {
    if ($time == 0) {
        return '';
    }
    $passedTime = time() - $time;
    if ($passedTime < 0) {
        return '';
    }
    elseif ($passedTime < 60) {
        return sprintf('%s초 전', $passedTime);
    }
    elseif ($passedTime < 3600) {
        return sprintf('%s분 %s초 전', floor($passedTime / 60), $passedTime % 60);
    }
    else {
        return sprintf('%s시간 전', floor($passedTime / 3600));
    }
}

function getQuoteStatusString($status=0) {
    if ($status < 0) {
        return '<strong class="text-primary" title="하락세">▼</strong>';
    }
    elseif ($status > 0) {
        return '<strong class="text-danger" title="상승세">▲</strong>';
    }
    else {
        return ''; //'<i class="fa fa-play text-success" aria-hidden="true"></i>';
    }
}

function compareQuoteCity($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['CITY'], $b['CITY']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareQuoteItem($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareTimeItem($a, $b) {
    if ($a['TIME'] == $b['TIME']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['TIME'] > $b['TIME']) ? -1 : 1;
}

function getListData($type, $name, $redis) {
    $result = array();
    $i = 0;
    if ($type == 'city') {
        $searchKey =  sprintf('도시:%s', $name);
        $listKeys = $redis->keys($searchKey);
        asort($listKeys);
        foreach ($listKeys as $cityKey) {
            $city = unserialize($redis->get($cityKey));
            $result[$i] = $city;
            $result[$i]['ITEMS'] = array();
            $items = $redis->keys($city['NAME'].':*');
            foreach ($items as $itemKey) {
                $result[$i]['ITEMS'][] = unserialize($redis->get($itemKey));
            }
            usort($result[$i]['ITEMS'], 'compareQuoteItem');
            $i++;
        }
    }
    elseif ($type == 'item') {
        $searchKey =  sprintf('*:%s', $name);
        $listKeys = $redis->keys($searchKey);
        foreach ($listKeys as $itemKey) {
            $result[$i] = unserialize($redis->get($itemKey));
            $result[$i]['CITY'] = str_replace(':'.$result[$i]['NAME'], '', $itemKey);
            $i++;
        }
        usort($result, 'compareQuoteCity');
    }
    else {
        $listKeys = $redis->keys('도시:*');
        asort($listKeys);
        foreach ($listKeys as $cityKey) {
            $city = unserialize($redis->get($cityKey));
            $result[$i] = $city;
            $result[$i]['ITEMS'] = array();
            $items = $redis->keys($city['NAME'].':*');
            asort($items);
            foreach ($items as $itemKey) {
                $result[$i]['ITEMS'][] = unserialize($redis->get($itemKey));
            }
            $result[$i]['COUNT'] = count($result[$i]['ITEMS']);
            usort($result[$i]['ITEMS'], 'compareTimeItem');
            if ((strlen($result[$i]['SALESTATUS']) == 0) && ($result[$i]['COUNT'] != 0)) {
                $result[$i]['TIME'] = $result[$i]['ITEMS'][0]['TIME'];
            }
            $i++;
        }
    }
    return $result;
}

function isCityInTrader($cityName) {
    $cityList = array(
        '간디아',
        '고어',
        '과테말라',
        '괌',
        '그랜드케이맨',
        '그로닝겐',
        '나탈',
        '나폴리',
        '낭트',
        '다바오',
        '단치히',
        '더블린',
        '도버',
        '도파르',
        '두알라',
        '디우',
        '딜리',
        '라구사',
        '라스팔마스',
        '람바예케',
        '런던',
        '로테르담',
        '로프부리',
        '루안다',
        '룬',
        '뤼베크',
        '르아브르',
        '리가',
        '리마',
        '리스본',
        '리우데자네이루',
        '마닐라',
        '마데이라',
        '마라카이보',
        '마르세이유',
        '마사와',
        '마술리파탐',
        '마카사르',
        '마카오',
        '말라가',
        '말라카',
        '말린디',
        '맨체스터',
        '메리다',
        '모가디슈',
        '모잠비크',
        '몸바사',
        '몽펠리에',
        '무스카트',
        '바르셀로나',
        '바스라',
        '바이아',
        '반자르마신',
        '발렌시아',
        '발파라이소',
        '베냉',
        '베네치아',
        '베라크루스',
        '베르겐',
        '베이루트',
        '벵가지',
        '벵겔라',
        '보르도',
        '보스턴',
        '부에노스아이레스',
        '브레멘',
        '브루나이',
        '비스뷔',
        '비아나두카스텔루',
        '빌바오',
        '사마라이',
        '사사리',
        '산안토니오',
        '산토도밍고',
        '산티아고',
        '산후안',
        '살로니카',
        '상투메',
        '상트 페테르부르크',
        '샌프란시스코',
        '세바스토폴',
        '세비야',
        '세우타',
        '세이라',
        '세인트조지스',
        '소코트라',
        '소팔라',
        '수라바야',
        '수에즈',
        '스톡홀름',
        '시라쿠사',
        '시에라리온',
        '실론',
        '아덴',
        '아르긴',
        '아비장',
        '아조레스',
        '아체',
        '아카풀코',
        '아테네',
        '안코나',
        '알렉산드리아',
        '알제',
        '암보이나',
        '암스테르담',
        '앤트워프',
        '야파',
        '에딘버러',
        '오데사',
        '오슬로',
        '왕가누이',
        '운대산',
        '윌렘스타트',
        '이스탄불',
        '자다르',
        '자메이카',
        '자카르타',
        '잔지바르',
        '잠비',
        '제노바',
        '제다',
        '중경',
        '지아딘',
        '천주',
        '카라카스',
        '카리비브',
        '카보베르데',
        '카사블랑카',
        '카옌',
        '카이로',
        '카카두',
        '카파',
        '칼레',
        '칼리아리',
        '칼비',
        '캘리컷',
        '캘커타',
        '케이프타운',
        '코친',
        '코콜라',
        '코토르',
        '코펜하겐',
        '코피아포',
        '쿠가리',
        '쿠칭',
        '킬와',
        '타마타브',
        '타코마',
        '테르나테',
        '툼베스',
        '튀니스',
        '트레비존드',
        '트루히요',
        '트리에스테',
        '트리폴리',
        '파나마',
        '파루',
        '파마구스타',
        '파타니',
        '팔렘방',
        '팔마',
        '페구',
        '페르남부쿠',
        '포르토벨로',
        '포르투',
        '포츠머스',
        '퐁디셰리',
        '프랑크 프루트',
        '플리머스',
        '피사',
        '핀자라',
        '하바나',
        '하와이',
        '함부르크',
        '항주',
        '헤르데르',
        '호르무즈',
        '호바트',
        '히바오아',
        '히혼',
    );
    return in_array($cityName, $cityList);
}
