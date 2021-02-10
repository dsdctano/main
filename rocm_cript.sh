sudo usermod -a -G video $LOGNAME
sudo apt-get install -y rocm-amdgpu-pro
echo 'export LLVM_BIN=/opt/amdgpu-pro/bin' | sudo tee /etc/profile.d/amdgpu-pro.sh
sudo sed -i 's/^GRUB_CMDLINE_LINUX_DEFAULT="/&amdgpu.vm_fragment_size=9 /'  /etc/default/grub
sudo update-grub