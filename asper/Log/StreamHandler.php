<?php
namespace Asper\Log;

use Monolog\Handler\StreamHandler as Handler;

class StreamHandler extends Handler{

	protected function write(array $record){
		if (!is_resource($this->stream)) {
			if (null === $this->url || '' === $this->url) {
				throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
			}
			$this->errorMessage = null;
			set_error_handler([$this, 'customErrorHandler']);
			
			$contents = file_exists($this->url) 
						 ? file_get_contents($this->url) 
						 : '';
			$contents .= (string) $record['formatted'];

			$flag = $this->useLocking ? LOCK_EX : 0;
			$result = file_put_contents($this->url, $contents, $flag);

			restore_error_handler();
		}

		if( is_resource($this->stream) ){
			if ($this->useLocking) {
				// ignoring errors here, there's not much we can do about them
				flock($this->stream, LOCK_EX);
			}
			$this->streamWrite($this->stream, $record);
			if ($this->useLocking) {
				flock($this->stream, LOCK_UN);
			}
		}
	}

	private function customErrorHandler($code, $msg)
    {
        $this->errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);
    }
}