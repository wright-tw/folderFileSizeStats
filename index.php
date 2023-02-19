<?php

// 取副檔名
function getSubFileName($sFileName)
{
	$sSubFileName = '無副檔名';
	$aFileName = explode('.', $sFileName);
	if (count($aFileName) == 1) {
		return $sSubFileName;
	}
	$aFileName = array_values(array_filter($aFileName));
	$sSubFileName = $aFileName[count($aFileName) - 1];
	return $sSubFileName;
}

function statsSizeByPath(&$aResult, $sPath)
{
	echo "正在處理目錄：" . $sPath . "\n";
	$aFiles = scandir($sPath);
	$aFiles = array_slice($aFiles, 2);
	foreach ($aFiles as $sFileName) {
		
		if ($sFileName == '.' || $sFileName == '..') {
			continue;
		}

		$sFullFilePath = $sPath . '/' . $sFileName;
		
		if (is_dir($sFullFilePath)) {
			// 資料夾
			statsSizeByPath($aResult, $sFullFilePath);
		} else {
			// 檔案
			$sSubFileName = getSubFileName($sFileName);
			if (isset($aResult[$sSubFileName])) {
				$aResult[$sSubFileName] += filesize($sFullFilePath);
			} else {
				$aResult[$sSubFileName] = filesize($sFullFilePath);
			}
		}
	}
}

$fStartTime = microtime(true);

// 處理路徑
$sPath = $argv[1] ?? dirname(__FILE__);
$sPath = rtrim($sPath, '/');
echo "將統計的目錄路徑" . $sPath . "\n";

if (! is_dir($sPath)) {
	echo "路徑不存在或不是個目錄" . "\n";
	return;
}

$aResult = [];
statsSizeByPath($aResult, $sPath);

// 統計大小
$iAllSize = 0;
ksort($aResult);
foreach ($aResult as $sFileType => $iFileSize) {
	echo $sFileType . '：' . number_format($iFileSize) . " Bytes\n";
	$iAllSize += $iFileSize;
}
echo '總儲存大小：' . number_format($iAllSize) . " Bytes\n\n";

$fUsedTime = microtime(true) - $fStartTime;
echo "總耗時：$fUsedTime 秒 \n";


