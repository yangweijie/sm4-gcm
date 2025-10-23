# 项目概述

此项目包含两个主要部分：

1. **kona-crypto**: 这是一个 Java 库，实现了中国的商用密码算法（ShangMi），包括 SM2、SM3 和 SM4。它基于腾讯的 Kona Crypto 项目，并提供了纯 Java 实现以及基于 JNI 和 OpenSSL 的实现。
2. **PHP SM4-GCM**: 这是一个 PHP 项目，实现了 SM4 算法的 GCM 模式，用于认证加密。

## Kona Crypto (Java)

### 简介

Tencent Kona Crypto 包含三个 Java 安全提供者：
*   `KonaCrypto` (纯 Java 实现)
*   `KonaCrypto-Native` (基于 JNI 和 OpenSSL)
*   `KonaCrypto-NativeOneShot` (基于 JNI 和 OpenSSL，但内存管理方式不同)

它实现了以下 ShangMi 算法：
*   **SM2**: 基于椭圆曲线密码学 (ECC) 的公钥算法。
*   **SM3**: 密码哈希算法。
*   **SM4**: 分组加密算法。

### 构建和运行

该项目使用 Gradle 作为构建工具。

*   **构建项目**: 在 `kona-crypto` 目录下运行 `./gradlew build`。
*   **运行测试**: 在 `kona-crypto` 目录下运行 `./gradlew test`。
*   **生成 JNI 头文件**: 在 `kona-crypto` 目录下运行 `./gradlew genJNIHeaders`。

### 关键文件和目录

*   `src/main/java/`: Java 源代码。
*   `src/main/jni/`: JNI 相关的 C 代码和头文件。
*   `src/test/java/`: 测试代码。
*   `build.gradle.kts`: Gradle 构建脚本。
*   `README.md`: 项目的详细说明文档。

## PHP SM4-GCM

### 简介

这是一个完整的 PHP 实现的 SM4-GCM 认证加密库。它提供了 SM4 块密码在 Galois/Counter Mode (GCM) 模式下的实现，用于提供数据机密性和真实性验证。

### 特性

*   SM4 块密码算法实现
*   GCM 模式用于认证加密
*   支持附加认证数据 (AAD)
*   安全参数生成
*   多种适配器（OpenSSL、内置模拟）
*   完整的异常处理机制
*   符合 PSR-4 自动加载标准

### 环境要求

*   PHP 7.4 或更高版本
*   OpenSSL 扩展（推荐以获得更好的性能）

### 目录结构

*   `src/`: PHP 源代码目录
  *   `Adapter/`: 适配器实现，包括 OpenSSL 适配器
  *   `Exceptions/`: 自定义异常类
  *   `SM4GCM.php`: 主要的 SM4-GCM 类
  *   `SM4GCMCipher.php`: 核心密码实现
  *   `SM4GCMParameterGenerator.php`: 参数生成器
  *   `SM4GCMParameters.php`: 参数封装类
  *   `SM4GCMParameterValidator.php`: 参数验证器
  *   `CryptoUtils.php`: 密码学工具类
*   `tests/`: 测试代码目录
*   `examples/`: 使用示例
*   `vendor/`: Composer 依赖包目录
*   `composer.json`: Composer 配置文件
*   `kona-crypto/`: 包含 Java 实现的 Kona Crypto 库

### 安装方法

#### 使用 Composer

```bash
composer require yangweijie/sm4-gcm
```

#### 手动安装

1. 克隆或下载代码库
2. 在你的 PHP 脚本中引入自动加载器：
   ```php
   require_once 'vendor/autoload.php';
   ```

### 使用方法

#### 基本加解密

```php
use yangweijie\SM4GCM\SM4GCM;
use yangweijie\SM4GCM\SM4GCMParameterGenerator;
use yangweijie\SM4GCM\CryptoUtils;

// 生成随机密钥
$key = CryptoUtils::secureRandom(16); // 128 位

// 生成参数
$paramGenerator = new SM4GCMParameterGenerator();
$params = $paramGenerator->generateParameters();

$iv = $params->getIV();

// 创建 SM4GCM 实例
$sm4gcm = new SM4GCM($key, $iv);

// 加密数据
$plaintext = "Hello, SM4-GCM!";
$ciphertext = $sm4gcm->encrypt($plaintext);

// 解密数据
$decrypted = $sm4gcm->decrypt($ciphertext);

echo $decrypted; // 输出: Hello, SM4-GCM!
```

#### 使用附加认证数据 (AAD)

```php
use yangweijie\SM4GCM\SM4GCM;

// 创建 SM4GCM 实例
$sm4gcm = new SM4GCM($key, $iv);

$aad = "Additional authenticated data";

// 使用 AAD 加密
$ciphertext = $sm4gcm->encrypt($plaintext, $aad);

// 使用 AAD 解密
$decrypted = $sm4gcm->decrypt($ciphertext, $aad);
```

### 测试

运行测试套件：

```bash
composer test
```

或使用 PHPUnit 直接运行：

```bash
./vendor/bin/phpunit
```

### API 参考

#### SM4GCM 类

##### 构造函数

```php
new SM4GCM(string $key, string $iv, int $tagLength = 128)
```

*   `$key`: 128 位加密密钥
*   `$iv`: 初始化向量（nonce）
*   `$tagLength`: 认证标签长度（32-128 位，8 的倍数）

##### 方法

###### encrypt

```php
encrypt(string $plaintext, string $aad = ''): string
```

使用 SM4-GCM 加密明文。

*   `$plaintext`: 要加密的数据
*   `$aad`: 附加认证数据（可选）
*   返回值: 包含认证标签的密文

###### decrypt

```php
decrypt(string $ciphertext, string $aad = ''): string
```

使用 SM4-GCM 解密密文。

*   `$ciphertext`: 要解密的数据（包含认证标签）
*   `$aad`: 附加认证数据（可选）
*   返回值: 解密后的明文

###### updateAAD

```php
updateAAD(string $aad): void
```

为后续操作设置附加认证数据。

*   `$aad`: 附加认证数据

###### reset

```php
reset(): void
```

重置 SM4GCM 实例到初始状态。

#### 辅助类

##### SM4GCMParameterGenerator

为 SM4-GCM 生成加密安全的参数。

###### 方法

####### generateIV

```php
generateIV(int $length = 12): string
```

生成随机初始化向量。

*   `$length`: IV 长度（字节，默认为 12）
*   返回值: 生成的 IV

####### generateParameters

```php
generateParameters(int $tagLength = 128, int $ivLength = null): SM4GCMParameters
```

生成包含随机值的 SM4GCMParameters。

*   `$tagLength`: 认证标签长度（位，默认为 128）
*   `$ivLength`: IV 长度（字节，默认为 12）
*   返回值: 生成的 SM4GCMParameters

##### CryptoUtils

提供密码学操作的工具函数。

###### 方法

####### toHex

```php
toHex(string $bytes): string
```

将字节转换为十六进制字符串。

*   `$bytes`: 要转换的字节
*   返回值: 十六进制表示

####### toBytes

```php
toBytes(string $hex): string
```

将十六进制字符串转换为字节。

*   `$hex`: 要转换的十六进制字符串
*   返回值: 字节表示

####### secureRandom

```php
secureRandom(int $length): string
```

生成加密安全的随机字节。

*   `$length`: 要生成的字节数
*   返回值: 随机字节

####### constantTimeEquals

```php
constantTimeEquals(string $a, string $b): bool
```

以恒定时间比较两个字符串以防止时序攻击。

*   `$a`: 要比较的第一个字符串
*   `$b`: 要比较的第二个字符串
*   返回值: 字符串相等返回 true，否则返回 false