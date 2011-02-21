<?php

L::import('WIND:core.WindComponentModule');
/**
 * 模板类
 * 职责：进行模板编译渲染
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindViewTemplate extends WindComponentModule {

	public $left_delimiter = "<?php";

	public $right_delimiter = "?>";

	/**
	 * 进行视图渲染
	 * 
	 * @param string $templateFile | 模板文件
	 * @param string $compileFile | 编译后生成的文件
	 * @param WindView $windView
	 */
	public function render($templateFile, $compileFile, $windView) {
		if (!$windView->getCompileDir()) {
			throw new WindViewException('compile dir is not exist \'' . $windView->getCompileDir() . '\' .');
		}
		if (!$this->checkReCompile($templateFile, $compileFile)) return null;
		$_output = $this->getTemplateFileContent($templateFile);
		$_output = $this->compile($_output);
		$this->cacheCompileResult($compileFile, $_output);
		return $_output;
	}

	/**
	 * 对模板内容进行编译
	 * @param string $content
	 */
	private function compile($content) {
		//TODO 
		$content = preg_replace_callback('/\?\>(.|\n)*?\<\?php/i', array($this, 'subCompile'), $content);
		
		return $content;
	}

	/**
	 * 编译匹配结果
	 */
	private function subCompile($content) {
		return $content;
	}

	/**
	 * 获得模板文件内容，目前只支持本地文件获取
	 * 
	 * @param string $templateFile
	 */
	private function getTemplateFileContent($templateFile) {
		$_output = '';
		if ($fp = @fopen($templateFile, 'r')) {
			while (!feof($fp)) {
				$_output .= fgets($fp, 4096);
			}
			fclose($fp);
		} else
			throw new WindViewException('Unable to open the template file \'' . $templateFile . '\'.');
		
		return $_output;
	}

	/**
	 * 检查是否需要重新编译
	 * 
	 * @param string $templateFile
	 * @param string $compileFile
	 */
	private function checkReCompile($templateFile, $compileFile) {
		$_reCompile = false;
		if (false === ($compileFileModifyTime = @filemtime($compileFile)))
			$_reCompile = true;
		else {
			$templateFileModifyTime = @filemtime($templateFile);
			if ((int) $templateFileModifyTime >= $compileFileModifyTime) $_reCompile = true;
		}
		return $_reCompile;
	}

	/**
	 * 将编译结果进行缓存
	 * @param string $compileFile | 编译缓存文件
	 * @param string $content | 模板内容
	 */
	private function cacheCompileResult($compileFile, $content) {
		L::import('WIND:component.utility.WindFile');
		WindFile::writeover($compileFile, $content);
	}

}

?>