<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import SignaturePad from 'signature_pad';
import axios from 'axios';

const props = defineProps({
    contract: Object,
    token: String
});

const showSigningModal = ref(false);
const customerSignaturePad = ref(null);
const witnessSignaturePad = ref(null);
const customerCanvas = ref(null);
const witnessCanvas = ref(null);
const showTermsAlert = ref(false);
const form = useForm({
    customer_signature: '',
    witness_signature: '',
    terms_accepted: false
});

const loading = ref(true);
const iframeBlocked = ref(false);

const resizeCanvas = async (canvas, pad) => {
    if (!canvas.value || !canvas.value.parentElement) {
        return;
    }

    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const canvasElement = canvas.value;
    const parentWidth = canvasElement.parentElement.offsetWidth - 32; // Subtract padding
    
    // Set canvas display size
    canvasElement.style.width = parentWidth + 'px';
    canvasElement.style.height = window.innerWidth < 640 ? '160px' : '192px'; // 40 or 48 * 4

    // Set canvas actual size
    canvasElement.width = parentWidth * ratio;
    canvasElement.height = (window.innerWidth < 640 ? 160 : 192) * ratio;
    
    // Scale canvas context
    const context = canvasElement.getContext('2d');
    context.scale(ratio, ratio);
    
    // Clear and reinitialize signature pad
    if (pad.value) {
        pad.value.clear();
    }
    pad.value = new SignaturePad(canvasElement, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'black',
        minWidth: 0.5,
        maxWidth: 2.5,
        throttle: 16,
        velocityFilterWeight: 0.2,
        onBegin: () => {
            // Ensure the canvas is properly cleared before starting a new signature
            context.clearRect(0, 0, canvasElement.width, canvasElement.height);
        }
    });

    // Add event listeners to handle touch/mouse events properly
    const preventScrolling = (e) => {
        e.preventDefault();
    };

    // Touch events
    canvasElement.addEventListener('touchstart', preventScrolling, { passive: false });
    canvasElement.addEventListener('touchmove', preventScrolling, { passive: false });
    canvasElement.addEventListener('touchend', preventScrolling, { passive: false });
    canvasElement.addEventListener('touchcancel', preventScrolling, { passive: false });

    // Mouse events
    canvasElement.addEventListener('mousedown', preventScrolling, { passive: false });
    canvasElement.addEventListener('mousemove', preventScrolling, { passive: false });
    canvasElement.addEventListener('mouseup', preventScrolling, { passive: false });

    // Handle window resize for mobile devices
    const handleResize = () => {
        const currentRatio = Math.max(window.devicePixelRatio || 1, 1);
        if (currentRatio !== ratio) {
            resizeCanvas(canvas, pad);
        }
    };

    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleResize);
};

const clearCustomerSignature = () => {
    if (customerSignaturePad.value) {
        customerSignaturePad.value.clear();
    }
};

const clearWitnessSignature = () => {
    if (witnessSignaturePad.value) {
        witnessSignaturePad.value.clear();
    }
};

const showTerms = () => {
    showTermsAlert.value = true;
};

const handleSubmit = () => {
    if (!customerSignaturePad.value || !witnessSignaturePad.value) {
        alert('Please provide both signatures');
        return;
    }

    if (!form.terms_accepted) {
        alert('Please accept the terms and conditions');
        return;
    }

    form.customer_signature = customerSignaturePad.value.toDataURL();
    form.witness_signature = witnessSignaturePad.value.toDataURL();
    axios.post(route('contract.process_sign', { token: props.token }), {
        customer_signature: form.customer_signature,
        witness_signature: form.witness_signature,
        terms_accepted: form.terms_accepted
    })
    .then(response => {
        if(response.data.success){
            window.location = route('contract.view', { id: props.token });
        }
    })
    .catch(error => {
        console.error(error);
        alert('There was an error processing the signature.');
    });
};
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

const debouncedResize = debounce(() => {
    if (showSigningModal.value) {
        resizeCanvas(customerCanvas, customerSignaturePad);
        resizeCanvas(witnessCanvas, witnessSignaturePad);
    }
}, 250);

// Watch for modal visibility changes
watch(showSigningModal, async (newValue) => {
    if (newValue) {
        // Wait for the next tick to ensure DOM is updated
        await nextTick();
        // Initialize signature pads
        await resizeCanvas(customerCanvas, customerSignaturePad);
        await resizeCanvas(witnessCanvas, witnessSignaturePad);
    }
});

const onIframeLoad = () => {
    loading.value = false;
};

const onIframeError = () => {
    loading.value = false;
    iframeBlocked.value = true;
};

const openContractInNewTab = () => {
    window.open(route('contract.view', { id: props.token }), '_blank');
};

onMounted(() => {
    window.addEventListener('resize', debouncedResize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', debouncedResize);
});
</script>

<template>
    <!-- <BreezeAuthenticatedLayout> -->
        <div class="min-h-screen w-full flex items-center justify-center bg-gray-50 p-4 sm:p-8">
            <div class="w-full max-w-4xl">
                <!-- Contract Preview Section -->
                <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 sm:mb-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-1">Contract Preview</h2>
                            <p class="text-xs sm:text-sm text-gray-500">Please review the contract carefully before proceeding to sign</p>
                        </div>
                        <div class="mt-2 sm:mt-0 flex items-center space-x-3">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-gray-100 text-gray-800">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Document ID: {{ contract.id }}
                            </div>
                            <Button 
                                @click="showSigningModal = true" 
                                class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg shadow-sm text-sm sm:text-base flex items-center"
                            >
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Sign Now
                            </Button>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">View Contract</h3>
                        <p class="text-sm text-gray-500 mb-4 text-center">Click the button below to view the contract in a new tab</p>
                        <Button 
                            @click="openContractInNewTab" 
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg shadow-sm text-sm flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Open Contract
                        </Button>
                    </div>
                </div>

                <!-- Signing Modal -->
                <div v-if="showSigningModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 border border-gray-200 max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Sign Contract</h2>
                            <button @click="showSigningModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm sm:text-base text-gray-600 mb-6">Both customer and witness signatures are required to complete this process</p>

                        <!-- Customer Signature Section -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <Label class="block text-sm font-medium text-gray-700">Customer Signature</Label>
                                <span class="text-xs text-gray-500">Required</span>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 bg-gray-50">
                                <canvas
                                    ref="customerCanvas"
                                    class="w-full h-40 sm:h-48 bg-white rounded-lg shadow-sm"
                                ></canvas>
                                <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
                                    <Button variant="outline" @click="clearCustomerSignature" class="text-gray-600 hover:bg-gray-100 text-sm border-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Clear Signature
                                    </Button>
                                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Draw your signature in the box above
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Witness Signature Section -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <Label class="block text-sm font-medium text-gray-700">Witness Signature</Label>
                                <span class="text-xs text-gray-500">Required</span>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 bg-gray-50">
                                <canvas
                                    ref="witnessCanvas"
                                    class="w-full h-40 sm:h-48 bg-white rounded-lg shadow-sm"
                                ></canvas>
                                <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
                                    <Button variant="outline" @click="clearWitnessSignature" class="text-gray-600 hover:bg-gray-100 text-sm border-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Clear Signature
                                    </Button>
                                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Draw witness signature in the box above
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="terms"
                                        type="checkbox"
                                        v-model="form.terms_accepted"
                                        class="h-4 w-4 text-gray-800 focus:ring-gray-500 border-gray-300 rounded"
                                    />
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700">
                                        I have read and agree to the 
                                        <button 
                                            @click="showTermsAlert = true" 
                                            class="text-gray-800 hover:text-gray-900 underline"
                                        >
                                            terms and conditions
                                        </button>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <Button 
                                @click="handleSubmit" 
                                class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg shadow-sm text-sm sm:text-base flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Sign Contract
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Terms Alert Modal -->
                <div v-if="showTermsAlert" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 border border-gray-200">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Terms and Conditions</h3>
                            <button @click="showTermsAlert = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="prose max-w-none text-sm text-gray-600 mb-4">
                            <p>By signing this contract, you agree to:</p>
                            <ul class="list-disc pl-5 mt-2">
                                <li>All terms and conditions outlined in the contract</li>
                                <li>Be legally bound by the agreement</li>
                                <li>Provide accurate and truthful information</li>
                                <li>Accept the terms as presented</li>
                            </ul>
                        </div>
                        <div class="flex justify-end">
                            <Button @click="showTermsAlert = false" class="bg-gray-800 hover:bg-gray-900 text-white">
                                I Understand
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- </BreezeAuthenticatedLayout> -->
</template> 