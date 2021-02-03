#!/usr/bin/env php
<?php
declare(strict_types = 1);
if (posix_geteuid () !== 0) {
	die ( "error: this script requires root privileges, re-run it as root." );
}
$amdgpu_dir = '/sys/bus/pci/drivers/amdgpu';
$dirs = array_filter ( array_map ( 'trim', glob ( $amdgpu_dir.DIRECTORY_SEPARATOR.'*', GLOB_NOSORT | GLOB_ONLYDIR | GLOB_MARK  ) ), function (string $str) {
	return (is_writable ( $str."power_dpm_force_performance_level" ) && is_writable ( $str.'pp_compute_power_profile' ));
} );
$max = count ( $dirs );
if ($max === 0) {
	die ( "error: found 0 applicable devices in $amdgpu_dir\n" );
}
echo "found $max applicable amdgpu devices\n";
$i = 0;
foreach ( $dirs as $gpu ) {
	++ $i;
	echo "setting card {$i}/{$max}: {$gpu}		..";
	my_write ( $gpu."power_dpm_force_performance_level", "auto" );
	my_write ( $gpu."pp_compute_power_profile", "set" );
	echo ". done\n";
}
die ( "finished!\n" );
function my_write(string $file, string $data, bool $append = false) {
	if (! is_writable ( $file )) {
		throw new \RuntimeException ( "$file is not writable!" );
	}
	$len = strlen ( $data );
	$written = file_put_contents ( $file, $data, $append ? FILE_APPEND : 0 );
	if ($written !== $len) {
		throw new \RuntimeException ( "tried to write $len bytes, but could only write $written bytes, file: $file" );
	}
	return $written;
}