/*******************************************************************************
 * Copyright 2019 pf-miles
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License.  You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
 * License for the specific language governing permissions and limitations under
 * the License.
 ******************************************************************************/
package com.github.pfmiles.dstier1.impl;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.nio.ByteBuffer;
import java.nio.ByteOrder;
import java.nio.channels.GatheringByteChannel;
import java.nio.channels.ScatteringByteChannel;
import java.nio.charset.Charset;
import java.util.ArrayList;
import java.util.List;

import io.netty.buffer.ByteBuf;
import io.netty.buffer.ByteBufAllocator;
import io.netty.buffer.ByteBufProcessor;
import io.netty.buffer.Unpooled;
import io.netty.util.internal.StringUtil;

/**
 * The helper class of the byteBuf change trick
 * 
 * @author pf-miles
 *
 */
public class SwappedByteBuf extends ByteBuf {
	// the current effecting byteBuf
	private ByteBuf cur;
	// the original byteBuf
	private ByteBuf orig;
	// byteBufs created during processing
	private List<ByteBuf> created = new ArrayList<>();

	public ByteBuf getCur() {
		return cur;
	}

	public ByteBuf getOrig() {
		return orig;
	}

	public List<ByteBuf> getCreated() {
		return created;
	}

	public SwappedByteBuf(ByteBuf orig) {
		this.orig = orig;
		this.cur = orig;
	}

	@Override
	public final boolean hasMemoryAddress() {
		return cur.hasMemoryAddress();
	}

	@Override
	public final long memoryAddress() {
		return cur.memoryAddress();
	}

	@Override
	public final int capacity() {
		return cur.capacity();
	}

	@Override
	public ByteBuf capacity(int newCapacity) {
		cur.capacity(newCapacity);
		return this;
	}

	@Override
	public final int maxCapacity() {
		return cur.maxCapacity();
	}

	@Override
	public final ByteBufAllocator alloc() {
		return cur.alloc();
	}

	@Override
	public final ByteOrder order() {
		return cur.order();
	}

	@Override
	public ByteBuf order(ByteOrder endianness) {
		return cur.order(endianness);
	}

	@Override
	public final ByteBuf unwrap() {
		return cur;
	}

	@Override
	public final boolean isDirect() {
		return cur.isDirect();
	}

	@Override
	public final int readerIndex() {
		return cur.readerIndex();
	}

	@Override
	public final ByteBuf readerIndex(int readerIndex) {
		cur.readerIndex(readerIndex);
		return this;
	}

	@Override
	public final int writerIndex() {
		return cur.writerIndex();
	}

	@Override
	public final ByteBuf writerIndex(int writerIndex) {
		cur.writerIndex(writerIndex);
		return this;
	}

	@Override
	public ByteBuf setIndex(int readerIndex, int writerIndex) {
		cur.setIndex(readerIndex, writerIndex);
		return this;
	}

	@Override
	public final int readableBytes() {
		return cur.readableBytes();
	}

	@Override
	public final int writableBytes() {
		return cur.writableBytes();
	}

	@Override
	public final int maxWritableBytes() {
		return cur.maxWritableBytes();
	}

	@Override
	public final boolean isReadable() {
		return cur.isReadable();
	}

	@Override
	public final boolean isWritable() {
		return cur.isWritable();
	}

	@Override
	public final ByteBuf clear() {
		cur.clear();
		return this;
	}

	@Override
	public final ByteBuf markReaderIndex() {
		cur.markReaderIndex();
		return this;
	}

	@Override
	public final ByteBuf resetReaderIndex() {
		cur.resetReaderIndex();
		return this;
	}

	@Override
	public final ByteBuf markWriterIndex() {
		cur.markWriterIndex();
		return this;
	}

	@Override
	public final ByteBuf resetWriterIndex() {
		cur.resetWriterIndex();
		return this;
	}

	@Override
	public ByteBuf discardReadBytes() {
		cur.discardReadBytes();
		return this;
	}

	@Override
	public ByteBuf discardSomeReadBytes() {
		cur.discardSomeReadBytes();
		return this;
	}

	@Override
	public ByteBuf ensureWritable(int minWritableBytes) {
		cur.ensureWritable(minWritableBytes);
		return this;
	}

	@Override
	public int ensureWritable(int minWritableBytes, boolean force) {
		return cur.ensureWritable(minWritableBytes, force);
	}

	@Override
	public boolean getBoolean(int index) {
		return cur.getBoolean(index);
	}

	@Override
	public byte getByte(int index) {
		return cur.getByte(index);
	}

	@Override
	public short getUnsignedByte(int index) {
		return cur.getUnsignedByte(index);
	}

	@Override
	public short getShort(int index) {
		return cur.getShort(index);
	}

	@Override
	public int getUnsignedShort(int index) {
		return cur.getUnsignedShort(index);
	}

	@Override
	public int getMedium(int index) {
		return cur.getMedium(index);
	}

	@Override
	public int getUnsignedMedium(int index) {
		return cur.getUnsignedMedium(index);
	}

	@Override
	public int getInt(int index) {
		return cur.getInt(index);
	}

	@Override
	public long getUnsignedInt(int index) {
		return cur.getUnsignedInt(index);
	}

	@Override
	public long getLong(int index) {
		return cur.getLong(index);
	}

	@Override
	public char getChar(int index) {
		return cur.getChar(index);
	}

	@Override
	public float getFloat(int index) {
		return cur.getFloat(index);
	}

	@Override
	public double getDouble(int index) {
		return cur.getDouble(index);
	}

	@Override
	public ByteBuf getBytes(int index, ByteBuf dst) {
		cur.getBytes(index, dst);
		return this;
	}

	@Override
	public ByteBuf getBytes(int index, ByteBuf dst, int length) {
		cur.getBytes(index, dst, length);
		return this;
	}

	@Override
	public ByteBuf getBytes(int index, ByteBuf dst, int dstIndex, int length) {
		cur.getBytes(index, dst, dstIndex, length);
		return this;
	}

	@Override
	public ByteBuf getBytes(int index, byte[] dst) {
		cur.getBytes(index, dst);
		return this;
	}

	@Override
	public ByteBuf getBytes(int index, byte[] dst, int dstIndex, int length) {
		cur.getBytes(index, dst, dstIndex, length);
		return this;
	}

	@Override
	public ByteBuf getBytes(int index, ByteBuffer dst) {
		cur.getBytes(index, dst);
		return this;
	}

	@Override
	public ByteBuf getBytes(int index, OutputStream out, int length) throws IOException {
		cur.getBytes(index, out, length);
		return this;
	}

	@Override
	public int getBytes(int index, GatheringByteChannel out, int length) throws IOException {
		return cur.getBytes(index, out, length);
	}

	@Override
	public ByteBuf setBoolean(int index, boolean value) {
		cur.setBoolean(index, value);
		return this;
	}

	@Override
	public ByteBuf setByte(int index, int value) {
		cur.setByte(index, value);
		return this;
	}

	@Override
	public ByteBuf setShort(int index, int value) {
		cur.setShort(index, value);
		return this;
	}

	@Override
	public ByteBuf setMedium(int index, int value) {
		cur.setMedium(index, value);
		return this;
	}

	@Override
	public ByteBuf setInt(int index, int value) {
		cur.setInt(index, value);
		return this;
	}

	@Override
	public ByteBuf setLong(int index, long value) {
		cur.setLong(index, value);
		return this;
	}

	@Override
	public ByteBuf setChar(int index, int value) {
		cur.setChar(index, value);
		return this;
	}

	@Override
	public ByteBuf setFloat(int index, float value) {
		cur.setFloat(index, value);
		return this;
	}

	@Override
	public ByteBuf setDouble(int index, double value) {
		cur.setDouble(index, value);
		return this;
	}

	@Override
	public ByteBuf setBytes(int index, ByteBuf src) {
		cur.setBytes(index, src);
		return this;
	}

	@Override
	public ByteBuf setBytes(int index, ByteBuf src, int length) {
		cur.setBytes(index, src, length);
		return this;
	}

	@Override
	public ByteBuf setBytes(int index, ByteBuf src, int srcIndex, int length) {
		cur.setBytes(index, src, srcIndex, length);
		return this;
	}

	@Override
	public ByteBuf setBytes(int index, byte[] src) {
		cur.setBytes(index, src);
		return this;
	}

	@Override
	public ByteBuf setBytes(int index, byte[] src, int srcIndex, int length) {
		cur.setBytes(index, src, srcIndex, length);
		return this;
	}

	@Override
	public ByteBuf setBytes(int index, ByteBuffer src) {
		cur.setBytes(index, src);
		return this;
	}

	@Override
	public int setBytes(int index, InputStream in, int length) throws IOException {
		return cur.setBytes(index, in, length);
	}

	@Override
	public int setBytes(int index, ScatteringByteChannel in, int length) throws IOException {
		return cur.setBytes(index, in, length);
	}

	@Override
	public ByteBuf setZero(int index, int length) {
		cur.setZero(index, length);
		return this;
	}

	@Override
	public boolean readBoolean() {
		return cur.readBoolean();
	}

	@Override
	public byte readByte() {
		return cur.readByte();
	}

	@Override
	public short readUnsignedByte() {
		return cur.readUnsignedByte();
	}

	@Override
	public short readShort() {
		return cur.readShort();
	}

	@Override
	public int readUnsignedShort() {
		return cur.readUnsignedShort();
	}

	@Override
	public int readMedium() {
		return cur.readMedium();
	}

	@Override
	public int readUnsignedMedium() {
		return cur.readUnsignedMedium();
	}

	@Override
	public int readInt() {
		return cur.readInt();
	}

	@Override
	public long readUnsignedInt() {
		return cur.readUnsignedInt();
	}

	@Override
	public long readLong() {
		return cur.readLong();
	}

	@Override
	public char readChar() {
		return cur.readChar();
	}

	@Override
	public float readFloat() {
		return cur.readFloat();
	}

	@Override
	public double readDouble() {
		return cur.readDouble();
	}

	@Override
	public ByteBuf readBytes(int length) {
		return cur.readBytes(length);
	}

	@Override
	public ByteBuf readSlice(int length) {
		return cur.readSlice(length);
	}

	@Override
	public ByteBuf readBytes(ByteBuf dst) {
		cur.readBytes(dst);
		return this;
	}

	@Override
	public ByteBuf readBytes(ByteBuf dst, int length) {
		cur.readBytes(dst, length);
		return this;
	}

	@Override
	public ByteBuf readBytes(ByteBuf dst, int dstIndex, int length) {
		cur.readBytes(dst, dstIndex, length);
		return this;
	}

	@Override
	public ByteBuf readBytes(byte[] dst) {
		cur.readBytes(dst);
		return this;
	}

	@Override
	public ByteBuf readBytes(byte[] dst, int dstIndex, int length) {
		cur.readBytes(dst, dstIndex, length);
		return this;
	}

	@Override
	public ByteBuf readBytes(ByteBuffer dst) {
		cur.readBytes(dst);
		return this;
	}

	@Override
	public ByteBuf readBytes(OutputStream out, int length) throws IOException {
		cur.readBytes(out, length);
		return this;
	}

	@Override
	public int readBytes(GatheringByteChannel out, int length) throws IOException {
		return cur.readBytes(out, length);
	}

	@Override
	public ByteBuf skipBytes(int length) {
		cur.skipBytes(length);
		return this;
	}

	@Override
	public ByteBuf writeBoolean(boolean value) {
		cur.writeBoolean(value);
		return this;
	}

	@Override
	public ByteBuf writeByte(int value) {
		cur.writeByte(value);
		return this;
	}

	@Override
	public ByteBuf writeShort(int value) {
		cur.writeShort(value);
		return this;
	}

	@Override
	public ByteBuf writeMedium(int value) {
		cur.writeMedium(value);
		return this;
	}

	@Override
	public ByteBuf writeInt(int value) {
		cur.writeInt(value);
		return this;
	}

	@Override
	public ByteBuf writeLong(long value) {
		cur.writeLong(value);
		return this;
	}

	@Override
	public ByteBuf writeChar(int value) {
		cur.writeChar(value);
		return this;
	}

	@Override
	public ByteBuf writeFloat(float value) {
		cur.writeFloat(value);
		return this;
	}

	@Override
	public ByteBuf writeDouble(double value) {
		cur.writeDouble(value);
		return this;
	}

	@Override
	public ByteBuf writeBytes(ByteBuf src) {
		cur.writeBytes(src);
		return this;
	}

	@Override
	public ByteBuf writeBytes(ByteBuf src, int length) {
		cur.writeBytes(src, length);
		return this;
	}

	@Override
	public ByteBuf writeBytes(ByteBuf src, int srcIndex, int length) {
		cur.writeBytes(src, srcIndex, length);
		return this;
	}

	@Override
	public ByteBuf writeBytes(byte[] src) {
		cur.writeBytes(src);
		return this;
	}

	@Override
	public ByteBuf writeBytes(byte[] src, int srcIndex, int length) {
		cur.writeBytes(src, srcIndex, length);
		return this;
	}

	@Override
	public ByteBuf writeBytes(ByteBuffer src) {
		cur.writeBytes(src);
		return this;
	}

	@Override
	public int writeBytes(InputStream in, int length) throws IOException {
		return cur.writeBytes(in, length);
	}

	@Override
	public int writeBytes(ScatteringByteChannel in, int length) throws IOException {
		return cur.writeBytes(in, length);
	}

	@Override
	public ByteBuf writeZero(int length) {
		cur.writeZero(length);
		return this;
	}

	@Override
	public int indexOf(int fromIndex, int toIndex, byte value) {
		return cur.indexOf(fromIndex, toIndex, value);
	}

	@Override
	public int bytesBefore(byte value) {
		return cur.bytesBefore(value);
	}

	@Override
	public int bytesBefore(int length, byte value) {
		return cur.bytesBefore(length, value);
	}

	@Override
	public int bytesBefore(int index, int length, byte value) {
		return cur.bytesBefore(index, length, value);
	}

	@Override
	public int forEachByte(ByteBufProcessor processor) {
		return cur.forEachByte(processor);
	}

	@Override
	public int forEachByte(int index, int length, ByteBufProcessor processor) {
		return cur.forEachByte(index, length, processor);
	}

	@Override
	public int forEachByteDesc(ByteBufProcessor processor) {
		return cur.forEachByteDesc(processor);
	}

	@Override
	public int forEachByteDesc(int index, int length, ByteBufProcessor processor) {
		return cur.forEachByteDesc(index, length, processor);
	}

	@Override
	public ByteBuf copy() {
		return cur.copy();
	}

	@Override
	public ByteBuf copy(int index, int length) {
		return cur.copy(index, length);
	}

	@Override
	public ByteBuf slice() {
		return cur.slice();
	}

	@Override
	public ByteBuf slice(int index, int length) {
		return cur.slice(index, length);
	}

	@Override
	public ByteBuf duplicate() {
		return cur.duplicate();
	}

	@Override
	public int nioBufferCount() {
		return cur.nioBufferCount();
	}

	@Override
	public ByteBuffer nioBuffer() {
		return cur.nioBuffer();
	}

	@Override
	public ByteBuffer nioBuffer(int index, int length) {
		return cur.nioBuffer(index, length);
	}

	@Override
	public ByteBuffer[] nioBuffers() {
		return cur.nioBuffers();
	}

	@Override
	public ByteBuffer[] nioBuffers(int index, int length) {
		return cur.nioBuffers(index, length);
	}

	@Override
	public ByteBuffer internalNioBuffer(int index, int length) {
		return cur.internalNioBuffer(index, length);
	}

	@Override
	public boolean hasArray() {
		return cur.hasArray();
	}

	@Override
	public byte[] array() {
		return cur.array();
	}

	@Override
	public int arrayOffset() {
		return cur.arrayOffset();
	}

	@Override
	public String toString(Charset charset) {
		return cur.toString(charset);
	}

	@Override
	public String toString(int index, int length, Charset charset) {
		return cur.toString(index, length, charset);
	}

	@Override
	public int hashCode() {
		return cur.hashCode();
	}

	@Override
	public boolean equals(Object obj) {
		return cur.equals(obj);
	}

	@Override
	public int compareTo(ByteBuf buffer) {
		return cur.compareTo(buffer);
	}

	@Override
	public String toString() {
		return StringUtil.simpleClassName(this) + '(' + cur.toString() + ')';
	}

	@Override
	public ByteBuf retain(int increment) {
		cur.retain(increment);
		return this;
	}

	@Override
	public ByteBuf retain() {
		cur.retain();
		return this;
	}

	@Override
	public final boolean isReadable(int size) {
		return cur.isReadable(size);
	}

	@Override
	public final boolean isWritable(int size) {
		return cur.isWritable(size);
	}

	@Override
	public final int refCnt() {
		return cur.refCnt();
	}

	@Override
	public boolean release() {
		return cur.release();
	}

	@Override
	public boolean release(int decrement) {
		return cur.release(decrement);
	}

	/**
	 * create a byteBuf using the specified byte array, and swap the current
	 * effecting byteBuf
	 */
	public void swapWriteBytes(byte[] data) {
		if (data == null) {
			data = new byte[0];
		}
		ByteBuf c = Unpooled.wrappedBuffer(data);
		this.created.add(c);
		this.cur = c;
	}
}
