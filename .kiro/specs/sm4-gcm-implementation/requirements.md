# Requirements Document

## Introduction

This document outlines the requirements for implementing SM4-GCM (Galois/Counter Mode) encryption and decryption functionality in PHP. The implementation will be based on the Java reference implementation from kona-crypto and will integrate with existing PHP cryptographic libraries (lpilp/guomi and yangweijie/gm-helper). The goal is to provide a complete, tested, and compatible SM4-GCM implementation that mirrors the functionality and test coverage of the Java version.

## Requirements

### Requirement 1: Core SM4-GCM Encryption/Decryption

**User Story:** As a PHP developer, I want to encrypt and decrypt data using SM4-GCM mode, so that I can secure sensitive information with authenticated encryption.

#### Acceptance Criteria

1. WHEN a user provides a 128-bit key, 96-bit IV, and plaintext THEN the system SHALL encrypt the data using SM4-GCM and return ciphertext with authentication tag
2. WHEN a user provides a 128-bit key, 96-bit IV, and valid ciphertext with tag THEN the system SHALL decrypt the data and return the original plaintext
3. WHEN a user provides invalid authentication tag THEN the system SHALL throw an authentication failure exception
4. WHEN a user provides an IV of incorrect length (not 96 bits) THEN the system SHALL throw an invalid parameter exception
5. WHEN a user provides a key of incorrect length (not 128 bits) THEN the system SHALL throw an invalid parameter exception

### Requirement 2: Additional Authenticated Data (AAD) Support

**User Story:** As a developer, I want to include additional authenticated data in SM4-GCM operations, so that I can authenticate metadata without encrypting it.

#### Acceptance Criteria

1. WHEN a user provides AAD during encryption THEN the system SHALL include it in authentication calculation without encrypting it
2. WHEN a user provides the same AAD during decryption THEN the system SHALL successfully authenticate and decrypt the data
3. WHEN a user provides different AAD during decryption THEN the system SHALL fail authentication and throw an exception
4. WHEN a user provides AAD in multiple segments THEN the system SHALL handle segmented AAD updates correctly
5. WHEN a user attempts to update AAD after starting encryption/decryption THEN the system SHALL throw an illegal state exception

### Requirement 3: Parameter Generation and Validation

**User Story:** As a developer, I want to generate secure SM4-GCM parameters automatically, so that I don't have to manually create cryptographically secure IVs.

#### Acceptance Criteria

1. WHEN a user requests parameter generation THEN the system SHALL generate a cryptographically secure 96-bit IV
2. WHEN a user requests parameter generation THEN the system SHALL create proper GCM parameter specifications with 128-bit tag length
3. WHEN parameters are encoded THEN the system SHALL use proper ASN.1 DER encoding format
4. WHEN encoded parameters are decoded THEN the system SHALL correctly reconstruct the original parameter values
5. WHEN invalid encoded parameters are provided THEN the system SHALL throw a decoding exception

### Requirement 4: Integration with Existing Libraries

**User Story:** As a developer, I want the SM4-GCM implementation to work seamlessly with existing yangweijie/gm-helper and lpilp/guomi libraries, so that I can leverage existing cryptographic infrastructure.

#### Acceptance Criteria

1. WHEN the system initializes THEN it SHALL successfully integrate with yangweijie/gm-helper library functions
2. WHEN the system performs SM4 operations THEN it SHALL utilize lpilp/guomi SM4 implementation as the underlying cipher
3. WHEN the system handles key operations THEN it SHALL be compatible with existing key management in gm-helper
4. WHEN the system processes data THEN it SHALL maintain compatibility with existing data format conventions
5. WHEN errors occur THEN the system SHALL provide meaningful error messages consistent with existing library patterns

### Requirement 5: Comprehensive Test Coverage

**User Story:** As a developer, I want comprehensive test coverage that mirrors the Java reference implementation, so that I can be confident in the correctness and compatibility of the PHP implementation.

#### Acceptance Criteria

1. WHEN tests are executed THEN they SHALL cover all SM4-GCM encryption/decryption scenarios from the Java test suite
2. WHEN tests run THEN they SHALL validate proper handling of empty data, null data, and edge cases
3. WHEN tests execute THEN they SHALL verify AAD functionality including segmented updates and error conditions
4. WHEN tests run THEN they SHALL check parameter generation, encoding, and decoding operations
5. WHEN tests execute THEN they SHALL validate tag mismatch scenarios and proper exception handling
6. WHEN tests run THEN they SHALL verify IV reuse detection and prevention
7. WHEN tests execute THEN they SHALL use Pest testing framework with clear, descriptive test names

### Requirement 6: Performance and Memory Management

**User Story:** As a developer, I want efficient SM4-GCM operations with proper memory management, so that the implementation is suitable for production use.

#### Acceptance Criteria

1. WHEN processing large data THEN the system SHALL handle data efficiently without excessive memory usage
2. WHEN operations complete THEN the system SHALL properly clean up sensitive data from memory
3. WHEN multiple operations run concurrently THEN the system SHALL maintain thread safety where applicable
4. WHEN cipher instances are reused THEN the system SHALL properly reset state between operations
5. WHEN errors occur THEN the system SHALL not leak sensitive information in error messages

### Requirement 7: Compatibility and Standards Compliance

**User Story:** As a developer, I want the implementation to be compatible with standard SM4-GCM specifications and the Java reference implementation, so that encrypted data can be exchanged between different implementations.

#### Acceptance Criteria

1. WHEN data is encrypted with the PHP implementation THEN it SHALL be decryptable by the Java kona-crypto implementation
2. WHEN data is encrypted with the Java implementation THEN it SHALL be decryptable by the PHP implementation
3. WHEN parameters are generated THEN they SHALL conform to SM4-GCM standard specifications
4. WHEN encoding/decoding operations occur THEN they SHALL follow standard ASN.1 DER format
5. WHEN tag lengths are used THEN they SHALL default to 128 bits as per standard recommendations