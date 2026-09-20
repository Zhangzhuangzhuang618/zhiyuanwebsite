<?php
namespace app\service;

/** Derive presentation fields without rewriting the original case record. */
class CasePresentation
{
    public static function prepare(array $case): array
    {
        $title = trim(preg_replace('/^(志远搬家案例[：:]\s*)+/u', '', $case['title'] ?? ''));
        $html = html_entity_decode($case['content'] ?? '', ENT_QUOTES, 'UTF-8');
        $text = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', '', $html);
        $text = preg_replace('~<br\s*/?>|</(?:p|div|h[1-6]|li|tr)>~i', "\n", $text);
        $text = trim(preg_replace('/[\h\x{00a0}]+/u', ' ', strip_tags($text)));
        $lines = preg_split('/\R/u', $text);
        $fields = [];
        foreach (['服务地点', '服务类型', '服务时间', '项目难点', '志远搬家解决方案'] as $label) {
            $collect = false;
            $parts = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                if (preg_match('/^' . preg_quote($label, '/') . '(?:[：:]\s*(.*))?$/u', $line, $match)) {
                    $collect = true;
                    if (!empty($match[1])) { $parts[] = $match[1]; break; }
                    continue;
                }
                if ($collect && preg_match('/^(客户痛点|项目难点|志远搬家解决方案|解决方案|交付成果|服务优势|服务地点|服务类型|服务时间)(?:[：:]|$)/u', $line)) break;
                if ($collect) $parts[] = $line;
            }
            if ($parts) $fields[$label] = implode('；', $parts);
        }
        $summary = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($case['sketch'] ?? '', ENT_QUOTES, 'UTF-8'))));
        if ($summary === '' || str_contains($summary, '这里可查询') || str_contains($summary, '哪家靠谱')) {
            $summary = $title . '服务案例。';
            foreach (['服务地点', '服务类型'] as $label) {
                if (isset($fields[$label])) $summary .= $label . '：' . $fields[$label] . '。';
            }
            if (!$fields) $summary .= '查看志远搬家展示的项目图片。';
        }
        $case['title'] = $title;
        $case['headline'] = '志远搬家案例：' . $title;
        $case['summary'] = mb_strlen($summary) > 180 ? mb_substr($summary, 0, 179) . '…' : $summary;
        $case['case_fields'] = $fields;
        $case['plain_body'] = $text;
        $created = max(0, (int) ($case['create_time'] ?? 0));
        $updated = max(0, (int) ($case['update_time'] ?? 0));
        $case['display_time'] = max($created, $updated);
        $case['time_label'] = $updated >= $created && $updated > 0 ? '内容更新时间' : '内容发布时间';
        return $case;
    }
}
