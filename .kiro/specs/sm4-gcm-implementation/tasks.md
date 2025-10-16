# Implementation Plan

- [ ] 1. Set up project structure and core interfaces
  - Create directory structure for classes, exceptions, and utilities
  - Define base interfaces and abstract classes for SM4-GCM operations
  - Set up autoloading configuration and namespace structure
  - _Requirements: 1.1, 4.4_

- [ ] 2. Implement exception classes and error handling
  - [ ] 2.1 Create SM4GCMException base class
    - Write base exception class with proper error codes and messages
    - Implement exception hierarchy for different error types
    - _Requirements: 1.3, 1.4, 1.5_

  - [ ] 2.2 Implement specific exception classes
    - Create InvalidParameterException for parameter validation errors
    - Create AuthenticationException for GCM tag verification failures
    - Create IllegalStateException for state management violations
    - Create EncodingException for ASN.1 encoding/decoding errors
    - _Requirements: 1.3, 1.4, 1.5, 3.5_

- [ ] 3. Create utility classes and helper functions
  - [ ] 3.1 Implement CryptoUtils class
    - Write hex/byte conversion functions (toHex, toBytes)
    - Implement secure random number generation
    - Create constant-time comparison function for authentication tags
    - _Requirements: 1.1, 6.2, 7.4_

  - [ ] 3.2 Write unit tests for CryptoUtils
    - Test hex/byte conversion with various inputs
    - Verify secure random generation produces different outputs
    - Test constant-time comparison with matching and non-matching inputs
    - _Requirements: 5.2_

- [ ] 4. Implement SM4GCMParameters class
  - [ ] 4.1 Create parameter validation and storage
    - Write constructor with IV and tag length validation
    - Implement getter methods for IV and tag length
    - Add parameter validation logic for IV length (96 bits) and tag length (128 bits)
    - _Requirements: 1.4, 1.5, 3.1, 3.2_

  - [ ] 4.2 Implement ASN.1 encoding and decoding
    - Write encode() method using genkgo/php-asn1 library
    - Implement static decode() method for parameter reconstruction
    - Handle encoding/decoding errors with proper exceptions
    - _Requirements: 3.3, 3.4, 3.5, 7.4_

  - [ ] 4.3 Write unit tests for SM4GCMParameters
    - Test parameter validation with valid and invalid inputs
    - Verify encoding/decoding round-trip operations
    - Test error handling for malformed encoded data
    - _Requirements: 5.1, 5.4_

- [ ] 5. Implement SM4GCMParameterGenerator class
  - [ ] 5.1 Create secure parameter generation
    - Write generateIV() method using cryptographically secure random
    - Implement generateParameters() method with proper defaults
    - Ensure generated IVs are unique and cryptographically secure
    - _Requirements: 3.1, 3.2_

  - [ ] 5.2 Write unit tests for parameter generation
    - Test IV generation produces 96-bit values
    - Verify generated parameters have correct tag length defaults
    - Test uniqueness of generated IVs across multiple calls
    - _Requirements: 5.1, 5.4_

- [ ] 6. Implement core SM4GCMCipher class
  - [ ] 6.1 Create cipher initialization and state management
    - Write init() method with key, IV, tag length, and mode parameters
    - Implement state management for encryption/decryption modes
    - Create reset() method to clear cipher state
    - Integrate with yangweijie/gm-helper for SM4 operations
    - _Requirements: 1.1, 1.2, 4.1, 4.2, 4.3_

  - [ ] 6.2 Implement GCM mode operations
    - Write GCM counter mode encryption/decryption logic
    - Implement GHASH authentication function
    - Create authentication tag generation and verification
    - Handle GCM state transitions properly
    - _Requirements: 1.1, 1.2, 1.3_

  - [ ] 6.3 Implement AAD (Additional Authenticated Data) handling
    - Write updateAAD() method for segmented AAD input
    - Implement AAD state management and finalization
    - Add validation to prevent AAD updates after data processing
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [ ] 6.4 Implement data processing methods
    - Write update() method for segmented data processing
    - Implement doFinal() method for final block processing and tag handling
    - Handle empty data and null input scenarios
    - _Requirements: 1.1, 1.2, 5.2_

  - [ ] 6.5 Write unit tests for SM4GCMCipher
    - Test cipher initialization with valid and invalid parameters
    - Verify GCM mode encryption/decryption operations
    - Test AAD handling including segmented updates
    - Test error conditions and state violations
    - _Requirements: 5.1, 5.2, 5.3, 5.5_

- [ ] 7. Implement main SM4GCM API class
  - [ ] 7.1 Create public API methods
    - Write constructor with key, IV, and tag length parameters
    - Implement encrypt() method for complete encryption operations
    - Implement decrypt() method for complete decryption operations
    - Add convenience methods for common use cases
    - _Requirements: 1.1, 1.2, 1.3_

  - [ ] 7.2 Integrate with SM4GCMCipher
    - Wire SM4GCM class to use SM4GCMCipher internally
    - Handle cipher state management and lifecycle
    - Implement proper error propagation from cipher to API
    - _Requirements: 1.1, 1.2, 4.1, 4.2_

  - [ ] 7.3 Write integration tests for SM4GCM
    - Test complete encryption/decryption workflows
    - Verify integration with underlying cipher implementation
    - Test error handling and exception propagation
    - _Requirements: 5.1, 5.5_

- [ ] 8. Implement library integration adapters
  - [ ] 8.1 Create yangweijie/gm-helper integration
    - Write adapter for SM4 block cipher operations
    - Implement key validation using gm-helper functions
    - Handle data format conversions between libraries
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

  - [ ] 8.2 Create lpilp/guomi integration
    - Integrate with guomi SM4 implementation for core operations
    - Handle any data format or API differences
    - Ensure compatibility with existing guomi conventions
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

  - [ ] 8.3 Write library integration tests
    - Test integration with yangweijie/gm-helper
    - Verify compatibility with lpilp/guomi
    - Test data format conversions and error handling
    - _Requirements: 5.1, 4.5_

- [ ] 9. Implement comprehensive test suite
  - [ ] 9.1 Create basic functionality tests
    - Write tests for all encryption/decryption scenarios
    - Test parameter validation and error conditions
    - Verify AAD handling in various configurations
    - _Requirements: 5.1, 5.2, 5.3_

  - [ ] 9.2 Create edge case and error handling tests
    - Test empty data, null inputs, and boundary conditions
    - Verify all exception scenarios are properly handled
    - Test IV reuse detection and prevention
    - Test tag manipulation and authentication failures
    - _Requirements: 5.2, 5.5, 5.6_

  - [ ] 9.3 Create compatibility tests
    - Write tests using Java kona-crypto test vectors
    - Verify cross-platform data exchange compatibility
    - Test parameter encoding/decoding compatibility
    - _Requirements: 5.1, 7.1, 7.2, 7.3_

  - [ ] 9.4 Create performance and security tests
    - Test large data handling and memory usage
    - Verify constant-time operations where applicable
    - Test concurrent usage scenarios
    - _Requirements: 6.1, 6.3, 6.4_

- [ ] 10. Finalize implementation and documentation
  - [ ] 10.1 Code review and optimization
    - Review all code for security best practices
    - Optimize performance-critical paths
    - Ensure proper memory management and cleanup
    - _Requirements: 6.1, 6.2, 6.4_

  - [ ] 10.2 Create usage examples and documentation
    - Write comprehensive usage examples
    - Create API documentation with proper annotations
    - Document integration patterns with existing libraries
    - _Requirements: 4.4, 4.5_

  - [ ] 10.3 Final validation and testing
    - Run complete test suite with coverage analysis
    - Perform final compatibility validation with Java implementation
    - Verify all requirements are met and documented
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 7.1, 7.2, 7.3, 7.4, 7.5_