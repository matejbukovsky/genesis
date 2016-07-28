<?php


namespace Genesis\Commands;


class NetteTester extends Command
{

	private $workingDir;

	private $target;

	private $options;


	/**
	 * Returns working directory. System will switch to this directory before running tests
	 * @return string
	 */
	public function getWorkingDir()
	{
		return $this->workingDir;
	}


	/**
	 * Sets working directory. System will switch to this directory before running tests
	 * @param mixed $workingDir
	 */
	public function setWorkingDir($workingDir)
	{
		$this->workingDir = $workingDir;
	}


	/**
	 * Returns target to be executed. Dir in working directory, dot (current dir), TestFile, etc
	 * @return mixed
	 */
	public function getTarget()
	{
		return $this->target;
	}


	/**
	 * Sets target to be executed. Dir in working directory, dot (current dir), TestFile, etc
	 * @param mixed $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}


	/**
	 * @return array|NULL
	 */
	public function getOptions()
	{
		return $this->options;
	}


	/**
	 * Possible options:
	 * - executable (mandatory)
	 * - iniFile
	 * - interpreter
	 * - threads
	 * - mode
	 * @param array|NULL $options
	 */
	public function setOptions(array $options = NULL)
	{
		$this->options = $options;
	}


	public function execute()
	{
		if (!isset($this->options['executable'])) {
			$this->error('Tester executable is not defined.');
		}

		$cmd = 'php ';
		$cmd .= escapeshellarg($this->options['executable']) . ' ';
		if (isset($this->options['iniFile'])) {
			$cmd .= '-c ';
			$cmd .= escapeshellarg($this->options['iniFile']) . ' ';
		}
		if (isset($this->options['interpreter'])) {
			$cmd .= '-p ';
			$cmd .= escapeshellarg($this->options['interpreter']) . ' ';
		}
		if (isset($this->options['threads'])) {
			$cmd .= '-j ';
			$cmd .= escapeshellarg(max(1, (int) $this->options['threads'])) . ' ';
		}
		if (isset($this->options['mode'])) {
			$cmd .= '-o ';
			$cmd .= escapeshellarg($this->options['mode']) . ' ';
		}
		$cmd .= '-s '; // show skipped tests
		$cmd .= escapeshellarg($this->target) . ' ';

		$currdir = getcwd();
		$result = @chdir($this->workingDir);
		if (!$result) {
			$this->error("Cannot change working directory to '$this->workingDir'.");
		}

		$command = new Exec();
		$command->setCommand($cmd);
		$result = $command->execute();
		if ($result->getResult() !== 0) {
			$this->error(sprintf('Tests failed with code %d.', $result->getResult()));
		}

		$result = @chdir($currdir);
		if (!$result) {
			$this->error("Cannot change working directory back to '$currdir'.");
		}
	}

}